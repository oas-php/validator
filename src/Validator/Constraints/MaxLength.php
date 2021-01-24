<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MaxLength extends Length implements SchemaPathAwareConstraint
{
    const TOO_LONG_ERROR = Length::TOO_LONG_ERROR;

    const TOO_LONG_MESSAGE = 'String "{{ value }}" should have less than one character'
                            . 'String "{{ value }}" should have less than {{ limit }} characters';
    public string $path;

    public $maxMessage = self::TOO_LONG_MESSAGE;

    public function __construct(int $value, string $path)
    {
        $this->path = "{$path}/maxLength";
        parent::__construct(
            [
                'max' => $value,
                'groups' => ['Default']
            ]
        );
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
