<?php declare(strict_types=1);

namespace OAS\Resolver;

use RuntimeException;

class UnreachableFragmentError extends RuntimeException
{
    public const CODE = 3;

    // TODO: perhaps including the evaluated schema would be useful?
    public function __construct(public readonly string $jsonPointer)
    {
        parent::__construct("Evaluation of JSON pointer \"$this->jsonPointer\" has failed", self::CODE);
    }
}
