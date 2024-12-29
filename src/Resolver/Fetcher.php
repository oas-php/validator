<?php declare(strict_types=1);

namespace OAS\Resolver;

interface Fetcher
{
    /**
     * @throws FetchingError
     */
    public function fetch(string $uri): string;
}
