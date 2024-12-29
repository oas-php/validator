<?php declare(strict_types=1);

namespace OAS\Resolver\Decoder;

use stdClass;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use OAS\Resolver\Decoder;
use OAS\Resolver\DecodingError;

class YamlDecoder implements Decoder
{
    private int $options;

    /**
     * @param int $options is passed to Symfony\Component\Yaml\Yaml::parse as second param
     */
    public function __construct(int $options = 0)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function decode(string $encoded): null|stdClass|array|bool|int|float
    {
        try {
            return Yaml::parse($encoded, $this->options);
        } catch (ParseException $exception) {
            throw new DecodingError($encoded, $exception->getMessage(), 0, $exception);
        }
    }
}
