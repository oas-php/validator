<?php declare(strict_types=1);

namespace OAS\Runner;

use LogicException;
use OAS\Runner\Report\Formatter;
use OAS\Runner\Report\Formatter\JsonFormatter;
use OAS\Runner\Report\Record;
use OAS\Runner\Report\Result;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use stdClass;
use Throwable;
use function iter\{filter, flatten, map};

class SchemaValidatorTestCase extends TestCase
{
    private static ?TestRunReport $report = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$report = new TestRunReport();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        assert(self::$report instanceof TestRunReport);

        $formatter = static::getFormatter();

        file_put_contents(
            static::getReportFilename(),
            $formatter->format(self::$report)
        );

        self::$report = null;
    }

    protected function assert(
        mixed $instance,
        stdClass|bool $schema,
        bool $isValid,
        string $schemaDescription,
        string $testDescription,
        string $filename,
        Version $version
    ): void
    {
        try {
            // start stopwatch
            $start = microtime(true);
            $result = match($isValid == $this->isValid($instance, $schema, $version)) {
                true => Result::SUCCESS,
                false => Result::FAILURE
            };
        } catch (Throwable $exception) {
            $result = Result::ERROR;
        } finally {
            $end = microtime(true);
        }

        assert(self::$report instanceof TestRunReport);

        self::$report->createTestResult(
                version: $version,
                filename: $filename,
                schemaDescription: $schemaDescription,
                testDescription: $testDescription,
                schema: $schema,
                instance: $instance,
                valid: $isValid,
                resultValue: $result,
                duration: $end - $start
        );

        if (isset($exception)) {
            throw $exception;
        }

        // TODO: provide a nice message
        self::assertTrue($result->isSuccess());
    }

    protected function isValid(mixed $instance, stdClass|bool $schema, Version $version): bool
    {
        throw new LogicException(
            sprintf('Method %::isValid() must be implemented.', __CLASS__)
        );
    }

    protected function baseDir(): string
    {
        return __DIR__ . '/../../tests/suites';
    }

    /**
     * @param ?callable(string): bool $filter
     * @return iterable<array{instance: mixed, schema: stdClass|bool, isValid: bool, schemaDescription: string, testDescription: string, version: Version}>
     */
    protected function readTestSuites(Version $version, callable $filter = null): iterable
    {
        $files = map(
            fn (array $wrapped) => $wrapped[0],
            new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator("{$this->baseDir()}/{$version->value}")
                ),
                pattern: '/^.+\.json/i',
                mode: RecursiveRegexIterator::GET_MATCH
            )
        );

        return flatten(
            map(
                fn (string $fileName) => array_map(
                    fn ($suite) => array_map(
                        fn (stdClass $test) => [
                            'instance' => $test->data,
                            'schema' => $suite->schema,
                            'isValid' => $test->valid,
                            'schemaDescription' => $suite->description,
                            'testDescription' => $test->description,
                            'fileName' => $fileName,
                            'version' => $version
                        ],
                        (array) $suite->tests
                    ),
                    (array) json_decode(
                        file_get_contents($fileName)
                    )
                ),
                $filter !== null ? filter($filter, $files) : $files
            ),
            levels: 2
        );
    }

    protected static function getReportFilename(): string
    {
        return getcwd() . '/report.json';
    }

    protected static function getFormatter(): Formatter
    {
        return new JsonFormatter();
    }
}