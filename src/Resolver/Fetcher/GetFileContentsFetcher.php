<?php declare(strict_types=1);

namespace OAS\Resolver\Fetcher;

use OAS\Resolver\Fetcher;
use OAS\Resolver\FetchingError;

class GetFileContentsFetcher implements Fetcher
{
    public function fetch(string $uri): string
    {
        $raw = @file_get_contents(urldecode($uri));

        if (false === $raw) {
            throw new FetchingError($uri, error_get_last()['message']);
        }

        return $raw;
    }
}