<?php declare(strict_types=1);

namespace OAS\Resolver;

use RuntimeException;

class UnreachableRefError extends RuntimeException
{
    public function __construct(
        public readonly Uri $src,
        public readonly Ref $ref,
        public readonly FetchingError|UnreachableAnchorError|UnreachableFragmentError $nestedError
    ) {
        parent::__construct(
            "The ref \"{$this->ref->value}\" defined at \"{$this->src}\" is unreachable",
            $nestedError->getCode(),
            $this->nestedError
        );
    }
}
