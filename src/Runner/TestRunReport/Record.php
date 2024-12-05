<?php declare(strict_types=1);

namespace OAS\Runner\Report;

use OAS\Runner\Version;
use stdClass;

class Record
{
    public function __construct(
        public readonly Version $version,
        public readonly string $schemaDescription,
        public readonly string $testDescription,
        public readonly stdClass|bool $schema,
        public readonly mixed $instance,
        public readonly bool $valid,
        public readonly Result $result,
        public readonly float $duration
    ) {
    }
}