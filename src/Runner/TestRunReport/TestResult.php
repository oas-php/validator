<?php declare(strict_types=1);

namespace OAS\Runner\Report;

class TestResult
{
    public function __construct(
        public readonly Result $value,
        public readonly string $description,
        public readonly mixed $instance,
        public readonly bool $valid,
        public readonly float $duration
    ) {
    }
}