<?php declare(strict_types=1);

namespace OAS\Resolver;

use stdClass;

interface Decoder
{
    /**
     * @throws DecodingError
     */
    public function decode(string $encoded): null|stdClass|array|bool|int|float;
}
