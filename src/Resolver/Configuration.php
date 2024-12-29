<?php declare(strict_types=1);

namespace OAS\Resolver;

use Psr\SimpleCache\CacheInterface;
use RuntimeException;

class Configuration
{
    public readonly Fetcher $fetcher;
    public readonly Decoder $decoder;
    public readonly ?CacheInterface $cache;

    public function __construct(
        ?Fetcher $fetcher = null,
        ?Decoder $decoder = null,
        ?CacheInterface $cache = null
    ) {
        $this->fetcher = $fetcher ?? new Fetcher\GetFileContentsFetcher();
        $this->decoder = $decoder ?? Decoder\Factory::create();

        try {
            $this->cache = $cache ?? Cache\Factory::create();
        } catch (RuntimeException) {
            $this->cache = null;
        }
    }
}
