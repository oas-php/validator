<?php declare(strict_types=1);

namespace OAS\Runner\Report;

class Stats
{
    public function __construct(
        public readonly int $testsCount,
        public readonly int $passedTestsCount,
        public readonly float $duration
    ) {
    }

    public static function sum(Stats ...$stats): Stats
    {
        /** @var array{int, int, float} $rawStats */
        $rawStats = array_reduce(
            $stats,
            function (array $acc, Stats $stats): array {
                $acc[0] += $stats->testsCount;
                $acc[1] += $stats->passedTestsCount;
                $acc[2] += $stats->duration;

                return $acc;
            },
            [0, 0, 0.0]
        );

        return new self(...$rawStats);
    }
}