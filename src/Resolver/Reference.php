<?php declare(strict_types=1);

namespace OAS\Resolver;

class Reference
{
    private Ref $ref;
    private Node $parent;
    private ?Node $resolved = null;
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

    public function resolve(Node $resolved): void
    {
        $this->resolved = $resolved;
    }

    public function getResolved(): ?Node
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
