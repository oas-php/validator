<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MinLength extends Length implements SchemaPathAwareConstraint
{
    const TOO_SHORT_ERROR = Length::TOO_SHORT_ERROR;

    const TOO_SHORT_MESSAGE = 'String "{{ value }}" should have more than one character'
                            . 'String "{{ value }}" should have more than one {{ limit }} characters';

    public string $path;

    public $minMessage = self::TOO_SHORT_ERROR;

    public function __construct(int $value, string $path)
    {
        $this->min = $value;
        $this->path = "{$path}/minLength";
    }

    public function validatedBy()
    {
        return LengthValidator::class;
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
