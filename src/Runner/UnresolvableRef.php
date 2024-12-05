<?php declare(strict_types=1);

namespace OAS\Runner;

use RuntimeException;
use Throwable;

class UnresolvableRef extends RuntimeException
{
    public function __construct(string $ref, Throwable $previous = null) {
        parent::__construct("Unresolvable ref: {$ref}", 0, $previous);
    }
}