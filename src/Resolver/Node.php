<?php declare(strict_types=1);

namespace OAS\Resolver;

use ArrayAccess;
use Generator;
use Iterator;
use IteratorAggregate;
use JsonSerializable;
use RuntimeException;
use stdClass;
use function OAS\Resolver\{decode, encode, pathSegments};
use function iter\{reduce, filter};

// TODO: review the node searching capabilities (find, get, has)
// TODO: to encode or not encode... that's the question
class Node implements ArrayAccess, JsonSerializable, IteratorAggregate
{
    private bool $processed = false;

    /**
     * @param ?array<string, Node|Reference|DynamicReference> $children
     */
    public function __construct(
        private Uri $uri,
        private ?Uri $canonicalUri = null,
        private ?array $children = null,
        private null|bool|string|int|float $value = null,
        private array $meta = [],
        private ?Node $parent = null
    ) {
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getCanonicalUri(): ?Uri
    {
        return $this->canonicalUri;
    }

    // TODO: examine all cases of usage carefully!
    // (make it recursive?)
    public function getCanonicalUriBase(): ?Uri
    {
        $node = $this;

        while ($node !== null && $node->canonicalUri === null) {
            $node = $node->parent;
        }

        return $node?->canonicalUri;
    }

    public function getValue(): null|bool|string|int|float
    {
        return $this->value;
    }

    /**
     * @return ?array<string, Node|Reference|DynamicReference>
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    public function isLeaf(): bool
    {
        return $this->children === null;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    public function hasChild(string $path): bool
    {
        return is_array($this->children) && array_key_exists($path, $this->children);
    }

    public function getChild(string $path): null|Node|Reference|DynamicReference
    {
        return $this->children[$path] ?? null;
    }

    public function addChild(string $path, Node|Reference|DynamicReference $node): void
    {
        $this->children[$path] = $node;
    }

    // TODO: examine uses and refactor (make it recursive?)
    // it does not start from root (even if the $jsonPoiner starts with "/")
    public function find(string $jsonPointer): Node|Reference
    {
        $currentNode = $this;
        $pathSegments = pathSegments($jsonPointer);

        while (!empty($pathSegments)) {
            $pathSegment = decode(
                array_shift($pathSegments)
            );

            switch ($pathSegment) {
                case '.':
                    break;

                case '..':
                    if ($currentNode->isRoot()) {
                        throw new UnreachableFragmentError($jsonPointer);
                    }

                    $currentNode = $currentNode->parent;
                    break;

                default:
                    if (!$currentNode instanceof Node || !$currentNode->hasChild($pathSegment)) {
                        throw new UnreachableFragmentError($jsonPointer);
                    }

                    $currentNode = $currentNode->getChild($pathSegment);
                    break;
            }
        }

        return $currentNode;
    }

    // TODO: is $contextUri a $scope?
    // TODO: remove anchor from name to make is more "generic" / agnostic
    // fall back to dynamicAnchor?
    public function findByAnchor(string $anchor, string $contextUri): Node
    {
        $node = $this->findByKeyAndValue('$anchor', $anchor, $contextUri)
            ?? $this->findByKeyAndValue('$dynamicAnchor', $anchor, $contextUri);

        if (is_null($node)) {
            throw new UnreachableAnchorError($anchor);
        }

        return $node;
    }

    /**
     * @return array<string, Node>
     */
    public function findAllByAnchor(string $dynamicAnchor): array
    {
        return reduce(
            /** @param array<string, Node> $nodes */
            function (array $nodes, Node $node) use ($dynamicAnchor) {
                $value = $node->getValue();
                // TODO: if canonical base is not known maybe we still need a root uri?
                $uri = (string) ($node->getCanonicalUriBase() ?? $node->getUri())->withoutFragment();

                if (is_array($value)) {
                    if (
                        array_key_exists('$dynamicAnchor', $value)
                        && $value['$dynamicAnchor']->getValue() === $dynamicAnchor
                    ) {
                        $nodes[$uri] = $node;
                    }
//                    else if (
//                        !array_key_exists($uri, $nodes)
//                        && array_key_exists('$anchor', $value)
//                        && $value['$anchor']->getValue() === $dynamicAnchor
//                    ) {
//                        $nodes[$uri] = $node;
//                    }
                }

                return $nodes;
            },
            $this,
            []
        );
    }

    public function getRoot(): Node
    {
        $node = $this;

        while (!$node->isRoot()) {
            $node = $node->parent;
        }

        return $node;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent);
    }

    public function denormalize(): mixed
    {
        if ($this->isLeaf()) {
            return $this->value;
        }

        // TODO: make it optional (bool $emitStdClassWhenEmptyObject = false ?)
        if (count($this->children) == 0 && ($this->meta['emptyObject'] ?? false)) {
            return new stdClass();
        }

        return array_map(
            fn (Node|Reference|DynamicReference $node) => $node instanceof Node
                ? $node->denormalize()
                : $node->getRef()->value,
            $this->children
        );
    }

    /** @return Iterator<int, Node> */
    public function getIterator(): Iterator
    {
        return $this->traverse();
    }

    /**
     * @return iterable<int, Node>
     */
    public function getNodeIterator(): iterable
    {
        return $this->traverse();
    }

    public function getProcessedNodeIterator(bool $rootsOnly = true): iterable
    {
        $iterator = $this->traverse();

        while ($iterator->valid()) {
            $node = $iterator->current();
            if ($node instanceof Node && $node->isProcessed()) {
                yield $iterator->key() => $node;
                $iterator->send(!$rootsOnly);
            } else {
                $iterator->send(true);
            }
        }
    }

    /**
     * @return iterable<int, DynamicReference|Reference>
     */
    public function getReferenceIterator(): iterable
    {
        if (!$this->isLeaf()) {
            foreach ($this->children ?? [] as $child) {
                if (!$child instanceof Node) {
                    yield $child;
                } else {
                    yield from $child->getReferenceIterator();
                }
            }
        }
    }

    /**
     * @return iterable<int, DynamicReference|Reference>
     */
    public function getUnprocessedReferenceIterator(): iterable
    {
        return filter(
            fn (Reference|DynamicReference $reference) => !$reference->isProcessed(),
            $this->getReferenceIterator()
        );
    }

    private function traverse(bool $continue = true): Generator
    {
        if ($continue) {
            $stop = (yield $this->getUri()->getFragment() ?? '/' => $this) === false;

            if (!($this->isLeaf() || $stop)) {
                foreach ($this->children ?? [] as $child) {
                    if ($child instanceof Node) {
                        yield from $child->traverse(!$stop);
                    }
                    // TODO perhaps it's not necessary!
//                    else {
//                        yield $child->getUri()->getFragment() => $child;
//                    }
                }
            }
        }
    }

    private function findByKeyAndValue(string $key, string $value, string $contextUri): ?Node
    {
        $contextUris = [(string) $this->uri];

        foreach ($this as $node) {
            $children = $node->getChildren();

            if (
                $children !== null
                && array_key_exists($key, $children)
                && $children[$key]->getValue() === $value
            ) {
                $canonicalUriBase =  $node->getCanonicalUriBase();
                $nodeContextUris = is_null($canonicalUriBase)
                    ? $contextUris
                    : array_merge($contextUris, [(string) $canonicalUriBase]);

                if (in_array($contextUri, $nodeContextUris)) {
                    return $node;
                }
            }
        }

        return null;
    }

    /** @internal */
    public function markAsProcessed(): void
    {
        foreach ($this as $node) {
            $node->processed = true;
        }
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function offsetExists($offset): bool
    {
        return $this->hasChild($offset);
    }

    public function offsetGet($offset): Node|Reference|null
    {
        return $this->getChild($offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Node is read only');
    }

    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Node is read only');
    }

    public function jsonSerialize(): mixed
    {
        return $this->denormalize();
    }
}
