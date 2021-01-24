<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Format extends Constraint
{
    public const FORMAT_MISMATCH_ERROR = '652b8af6-48a9-4117-a1f5-59379c57ae84';

    public const FORMAT_MISMATCH_MESSAGE = 'Value "{{ value }}" does not validate against "{{ format }}" format';

    public const UNSUPPORTED_FORMAT_ERROR = '87e04746-9612-4c3a-aef1-f99bfdb4eef5';

    public const UNSUPPORTED_FORMAT_MESSAGE = 'Format "{{ format }}" is not supported for "{{ type }}" type';

    public string $format;

    public function __construct(string $format, string $path, Configuration $configuration)
    {
        $this->format = $format;
        parent::__construct("{$path}/format", $configuration);
    }
}
