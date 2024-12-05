<?php declare(strict_types=1);

namespace OAS;

use OAS\Validator\Configuration;
use OAS\Validator\Symfony\Result;
use OAS\Validator\SchemaConformanceFailure;
use stdClass;

interface Validator
{
    public function validate(
        stdClass|array|string|int|float|bool|null $instance,
        Schema|bool $schema,
        Configuration $configuration = null
    ): Result;

    /**
     * @throws SchemaConformanceFailure
     */
    public function assertValid(
        stdClass|array|string|int|float|bool|null $instance,
        Schema|bool $schema,
        Configuration $configuration = null
    ): void;
}