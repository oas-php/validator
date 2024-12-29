<?php declare(strict_types=1);

namespace OAS\Resolver;

use RuntimeException;

class UnreachableAnchorError extends RuntimeException
{
    public const CODE = 2;

    // TODO: perhaps including the evaluated schema would be useful?
    public function __construct(public readonly string $anchor)
    {
        parent::__construct("The anchor \"$anchor\" has not been found", self::CODE);
    }
}
