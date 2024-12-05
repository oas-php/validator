<?php declare(strict_types=1);

use OAS\Runner\Report\Formatter;
use OAS\Runner\SchemaValidatorTestCase;
use OAS\Runner\Version;
use OAS\Schema;
use OAS\Resolver;
use OAS\Validator\Symfony\Validator;

class OfficialSuiteSchemaValidatorTest extends SchemaValidatorTestCase
{
    private Schema\Factory $factory;
    private Validator $validator;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->factory = new Schema\Factory(new Resolver());
        $this->validator = new Validator();
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function draft2020_12(
        mixed $instance,
        stdClass|bool $schema,
        bool $isValid,
        string $schemaDescription,
        string $testDescription,
        string $filename,
        Version $version
    ): void {
        $this->assert($instance, $schema, $isValid, $schemaDescription, $testDescription, $filename, $version);
    }

    public function draft2020_12TestDataProvider(): iterable
    {
        $draft202012 = Version::DRAFT_2020_12;
        $whitelist = [
            "{$this->baseDir()}/$draft202012->value/anchor.json",
            "{$this->baseDir()}/$draft202012->value/refRemote.json"
        ];

        return $this->readTestSuites($draft202012, fn (string $filename) => in_array($filename, $whitelist));
    }

    public function dataProvider(): iterable
    {
        $draft201909 = Version::DRAFT_2019_09;
        $draft201909Whitelist = [
            "{$this->baseDir()}/$draft201909->value/maximum.json",
            "{$this->baseDir()}/$draft201909->value/minimum.json"
        ];
        $draft202012 = Version::DRAFT_2020_12;
        $draft202012Whitelist = [
            "{$this->baseDir()}/$draft202012->value/refRemote.json",
            "{$this->baseDir()}/$draft202012->value/dynamicRef.json",
            "{$this->baseDir()}/$draft202012->value/minimum.json"
        ];

        yield from $this->readTestSuites($draft201909, fn (string $filename) => in_array($filename, $draft201909Whitelist));
        yield from $this->readTestSuites($draft202012, fn (string $filename) => in_array($filename, $draft202012Whitelist));
    }

    protected function isValid(mixed $instance, stdClass|bool $schema, $version): bool
    {
        try {
            $this->validator->validate(
                $instance,
                $this->factory->createFromPrimitives(
                    $schema
                )
            );

            $valid = true;
        } catch (Validator\SchemaConformanceFailure $exception) {
            $valid = false;
        }

        return $valid;
    }

    protected static function getFormatter(): Formatter
    {
        return new Formatter\BootstrapHtmlFormatter();
    }

    protected static function getReportFilename(): string
    {
        return getcwd() . '/report.html';
    }
}