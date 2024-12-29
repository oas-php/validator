<?php declare(strict_types=1);

namespace OAS;

use OAS\Resolver\Configuration;
use OAS\Resolver\DecodingError;
use OAS\Resolver\FetchingError;
use OAS\Resolver\Node;
use OAS\Resolver\Reference;
use OAS\Resolver\UndecodableRefError;
use OAS\Resolver\UnreachableAnchorError;
use OAS\Resolver\UnreachableFragmentError;
use OAS\Resolver\UnreachableRefError;
use OAS\Resolver\Uri;
use stdClass;
use function iter\reduce;

// TODO: improve caching
//  - implement simple array-based caching
//  - implement clear method to clear caching
class Resolver
{
    private Configuration $configuration;
    private Node\Factory $nodeFactory;
    /** @var array<string, Node> */
    private array $fetched = [];

    public function __construct(?Configuration $configuration = null)
    {
        $this->configuration = $configuration ?? new Configuration();
        $this->nodeFactory = new Node\Factory();
    }

    public function resolve(string $uri): Node
    {
        return $this->doResolve(
            new Uri($uri)
        );
    }

    public function resolveDecoded($decoded, string $uri = null): Node
    {
        $uri = new Uri($uri ?? getcwd());
        $root = $this->nodeFactory->create($decoded, $uri);

        $this->markFetched($root);
        $this->resolveRefs($root);

        return $root;
    }

    public function resolveEncoded(string $encoded, string $uri = null): Node
    {
        $uri = new Uri($uri ?? getcwd());
        $root = $this->nodeFactory->create(
            $this->decode(
                $encoded
            ),
            $uri
        );

        $this->markFetched($root);
        $this->resolveRefs($root);

        return $root;
    }

    public function clear(): void
    {
        $this->fetched = [];
        $this->configuration->cache?->clear();
    }

    private function doResolve(Uri $uri): Node
    {
        $uriWithoutFragment = $uri->withoutFragment();
        $node = $this->nodeFactory->create(
            $this->getFromCache(
                (string) $uriWithoutFragment,
                fn () => $this->decode(
                    $this->fetch($uriWithoutFragment)
                )
            ),
            $uriWithoutFragment
        );

        $this->markFetched($node);

        $fragment = $uri->getFragment();

        if ($fragment !== null) {
            $node = $uri->hasAnchor()
                ? $node->findByAnchor($fragment, (string) $uriWithoutFragment)
                : $node->find($fragment);
        }

        $this->resolveRefs($node);

        return $node;
    }

    /**
     * @throws UnreachableRefError
     * @throws UnreachableFragmentError
     */
    private function resolveRefs(Node $graph): void
    {
        if (!$graph->isProcessed()) {
            $graph->markAsProcessed();

            foreach ($graph->getUnprocessedReferenceIterator() as $reference) {
                $reference->markAsProcessed();
                $ref = $reference->getRef();

                try {
                    $reference->resolve(
                        $reference instanceof Reference
                            ? $this->resolveRef($ref->resolved)
                            : $this->resolveDynamicRef($ref->resolved)
                    );
                } catch (DecodingError $decodingException) {
                    throw new UndecodableRefError(
                        $reference->getParent()->getUri(),
                        $ref,
                        $decodingException
                    );
                } catch (FetchingError|UnreachableAnchorError|UnreachableFragmentError $error) {
                    throw new UnreachableRefError(
                        // TODO: perhaps the canonical uri could be provided here?
                        $reference->getParent()->getUri(),
                        $ref,
                        $error
                    );
                }
            }
        }
    }

    /**
     * @throws UnreachableFragmentError
     * @throws UnreachableAnchorError
     */
    private function resolveRef(Uri $uri): Node
    {
        $withoutFragment = (string) $uri->withoutFragment();
        $fetched = $this->fetched[$withoutFragment] ?? null;

        if (null !== $fetched) {
            //$this->resolveRefs($fetched);
            $fragment = $uri->getFragment();

            if ($fragment !== null) {
                $resolved = $uri->hasAnchor()
                    ? $fetched->findByAnchor($fragment, $withoutFragment)
                    : $fetched->find($fragment);
            } else {
                $resolved = $fetched;
            }

            $this->resolveRefs($resolved);
        } else {
            $resolved = $this->doResolve($uri);
        }

        return $resolved;
    }

    /**
     * TODO: the exceptions are not actually thrown (an empty list is returned)
     *
     * @throws UnreachableFragmentError
     * @throws UnreachableAnchorError
     * @return Node|array<string, Node>
     */
    private function resolveDynamicRef(Uri $uri): Node|array
    {
        $withoutFragment = (string) $uri->withoutFragment();
        $fragment = $uri->getFragment();

        if ($uri->hasAnchor()) {
            if (!array_key_exists($withoutFragment, $this->fetched)) {
                $this->doResolve($uri);
            }

            $resolved = reduce(
                fn (array $resolved, Node $node) => array_merge($resolved, $node->findAllByAnchor($fragment)),
                // TODO: perhaps it should run on root only (then reduce would not be necessary)
                $this->fetched,
                []
            );

            if (count($resolved) > 0) {
                return $resolved;
            }
        }

        return $this->resolveRef($uri);
    }

    private function fetch(Uri $uri): string
    {
        return $this->configuration->fetcher->fetch((string) $uri);
    }

    private function decode(string $encoded): null|stdClass|array|bool|int|float
    {
        return $this->configuration->decoder->decode($encoded);
    }

    private function getFromCache(string $key, callable $getValue): mixed
    {
        $cache = $this->configuration->cache;

        if ($cache !== null) {
            $key = md5($key);

            if (!$cache->has($key)) {
                $cache->set($key, call_user_func($getValue));
            }

            $value = $cache->get($key);
        } else {
            $value = call_user_func($getValue);
        }

        return $value;
    }

    private function markFetched(Node $node): void
    {
        if (is_null($node->getCanonicalUri())) {
            // TODO is ->withoutFragment necessary?
            $this->fetched[(string) ($node->getUri()->withoutFragment())] = $node;
        }

        /** @var Node $child */
        foreach ($node as $child) {
            $nodeCanonicalUri = $child->getCanonicalUri();

            if (!is_null($nodeCanonicalUri)) {
                $this->fetched[(string) $nodeCanonicalUri] = $child;
            }
        }
    }
}