<?php declare(strict_types=1);

namespace OAS\Resolver;

class Ref
{
    public function __construct(public readonly string $value, public Uri $resolved)
    {
    }
}