<?php declare(strict_types=1);

namespace OAS\Runner\Report;

use OAS\Runner\Version;
use stdClass;

class SchemaTestReport
{
    /**
     * @param array<int, TestResult> $testResults
     */
    public function __construct(
        public Version $version,
        public readonly stdClass|bool $schema,
        public readonly string $description,
        public readonly string $source,
        public array $testResults = []
    ) {
    }

    public function addTestResult(TestResult $testEntry): void
    {
        $this->testResults[] = $testEntry;
    }

    /**
     * @return array<int, TestResult>
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function getStats(): Stats
    {
        /** @var array{int, int, float} $rawStats */
        $rawStats = array_reduce(
            $this->testResults,
            /**
             * @return array{int, int, float}
             */
            function (array $acc, TestResult $testResult): array {
                $acc[0]++;
                if ($testResult->value->isSuccess()) {
                    $acc[1]++;
                }
                $acc[2] += $testResult->duration;

                return $acc;
            },
            [0, 0, 0.0]
        );

        return new Stats(...$rawStats);
    }
}