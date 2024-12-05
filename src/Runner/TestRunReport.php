<?php declare(strict_types=1);

namespace OAS\Runner;

use OAS\Runner\Report\SchemaTestReport;
use OAS\Runner\Report\Result;
use OAS\Runner\Report\Stats;
use OAS\Runner\Report\TestResult;
use RuntimeException;
use stdClass;

class TestRunReport
{
    /**
     * @param array<string, array<string, SchemaTestReport>> $schemaTestReportsByVersion
     */
    public function __construct(private array $schemaTestReportsByVersion = [])
    {
    }

    public function createTestResult(
        Version $version,
        string $filename,
        string $schemaDescription,
        string $testDescription,
        stdClass|bool $schema,
        mixed $instance,
        bool $valid,
        Result $resultValue,
        float $duration
    ): void {
        $schemaKey = md5($filename.$schemaDescription);

        if (!array_key_exists($version->value, $this->schemaTestReportsByVersion)) {
            $this->schemaTestReportsByVersion[$version->value] = [];
        }

        if (!array_key_exists($schemaKey, $this->schemaTestReportsByVersion[$version->value])) {
            $this->schemaTestReportsByVersion[$version->value][$schemaKey] = new SchemaTestReport(
                $version,
                $schema,
                $schemaDescription,
                $filename
            );
        }

        $this->schemaTestReportsByVersion[$version->value][$schemaKey]->addTestResult(
            new TestResult(
                $resultValue,
                $testDescription,
                $instance,
                $valid,
                $duration
            )
        );
    }

    public function getStats(): Stats
    {
        return Stats::sum(
            ...array_map(
                fn (string $version, SchemaTestReport $schemaTestReport) => $this->getStatsByVersion($version),
                $this->schemaTestReportsByVersion,
                array_keys($this->schemaTestReportsByVersion)
            )
        );
    }

    /**
     * @throws RuntimeException
     */
    public function getStatsByVersion(string $version): Stats
    {
        if (array_key_exists($version, $this->schemaTestReportsByVersion)) {
            return Stats::sum(
                ...array_map(
                    fn (array $schemaTestReports) => Stats::sum(
                        ...array_map(
                            fn (SchemaTestReport $schemaTestReport) => $schemaTestReport->getStats(),
                            $schemaTestReports
                        )
                    ),
                    $this->schemaTestReportsByVersion[$version]
                )
            );
        }

        throw new RuntimeException("No schema tests were executed for given version: {$version}");
    }

    /**
     * @return array<string, array<string, SchemaTestReport>>
     */
    public function getSchemaTestReports(): array
    {
        return $this->schemaTestReportsByVersion;
    }
}