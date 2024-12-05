<?php declare(strict_types=1);

namespace OAS\Runner\Report\Formatter;

use OAS\Runner\TestRunReport;
use OAS\Runner\Report\Record;
use OAS\Runner\Report\Result;
use stdClass;

class ArrayFormatter
{
    public function format(TestRunReport $report): array
    {
        return array_map(
            function (TestRunReport $report) {
                $stats = $report->getStats();

                return [
                    'stats' => [
                        'total' => $stats->testsCount,
                        'passed' => $stats->passedTestsCount
                    ],
                    'results' => $this->groupBySchema($report)
                ];
            },
            $report->groupByVersion()
        );
    }

    /**
     * @return array<int, array{description: string, schema: stdClass|bool, tests: array<int, array{description: string, instance: mixed, valid: bool, success: bool, duration: float}>}>
     */
    private function groupBySchema(TestRunReport $report): array
    {
        return array_values(
            array_reduce(
                $report->getSchemaTestReports(),
                function (array $recordsGroupedBySchema, Record $record): array {
                    $schemaKey = md5(json_encode($record->schema));

                    if (!array_key_exists($schemaKey, $recordsGroupedBySchema)) {
                        $recordsGroupedBySchema[$schemaKey] = [
                            'description' => $record->schemaDescription,
                            'schema' => $record->schema,
                            'tests' => []
                        ];
                    }

                    $recordsGroupedBySchema[$schemaKey]['tests'][] = [
                        'description' => $record->testDescription,
                        'instance' => $record->instance,
                        'valid' => $record->valid,
                        'result' => match ($record->result) {
                            Result::SUCCESS => 'passed',
                            Result::FAILURE => 'failed',
                            Result::ERROR => 'error'
                        },
                        'duration' => $record->duration
                    ];

                    return $recordsGroupedBySchema;
                },
                []
            )
        );
    }
}