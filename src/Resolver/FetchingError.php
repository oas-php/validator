<?php declare(strict_types=1);

namespace OAS\Resolver;

use RuntimeException;

class FetchingError extends RuntimeException
{
    public function __construct(public readonly string $uri, public readonly string $reason)
    {
        parent::__construct("Resource is not reachable under \"$uri\" due to: \"$reason\"");
    }
}
