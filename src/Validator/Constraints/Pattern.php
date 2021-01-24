<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Pattern extends Regex implements SchemaPathAwareConstraint
{
    const PATTERN_MISMATCH_ERROR = Regex::REGEX_FAILED_ERROR;

    const PATTERN_MISMATCH_MESSAGE = 'Value "{{ value }}" does not match the pattern';

    public string $path;

    public $message = self::PATTERN_MISMATCH_MESSAGE;

    public function __construct(string $value, string $path)
    {
        $this->path = "{$path}/pattern";
        parent::__construct(
            [
                'pattern' => $value,
                'groups' => ['Default']
            ]
        );
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
