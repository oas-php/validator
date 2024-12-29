<?php declare(strict_types=1);

namespace OAS\Resolver;

use RuntimeException;

class UndecodableRefError extends RuntimeException
{
    public function __construct(
        private Uri $src,
        private Ref $ref,
        DecodingError $decodingException
    ) {
        parent::__construct(
            sprintf(
                'Resource fetched from URI: %s could not be decoded using configured decoders (source: %s, $ref: %s)',
                $this->ref->resolved->withoutFragment(),
                $this->src,
                $this->ref->value,
            ),
            previous: $decodingException
        );
    }

    public function src(): Uri
    {
        return $this->src;
    }

    public function ref(): Ref
    {
        return $this->ref;
    }

    public function resource(): string
    {
        /** @var DecodingError $decodingException */
        $decodingException = $this->getPrevious();

        return $decodingException->resource();
    }
}
