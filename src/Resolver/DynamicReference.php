<?php declare(strict_types=1);

namespace OAS\Resolver;

use OAS\Resolver\Node;
use OAS\Resolver\Ref;

class DynamicReference
{
    private Ref $ref;
    private Node $parent;
    /** @var array<int, Node> $resolved */
    private Node|array $resolved = [];
    private bool $processed = false;

    public function __construct(Ref $ref, Node $parent)
    {
        $this->ref = $ref;
        $this->parent = $parent;
    }

    public function getRef(): Ref
    {
        return $this->ref;
    }

    public function getParent(): Node
    {
        return $this->parent;
    }

    /**
     * @param Node|array<string, Node> $resolved
     */
    public function resolve(Node|array $resolved): void
    {
        $this->resolved = $resolved;
    }

    /** @return Node|array<string, Node> */
    public function getResolved(): Node|array
    {
        return $this->resolved;
    }

    /** @internal */
    public function markAsProcessed(): void
    {
        $this->processed = true;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }
}
