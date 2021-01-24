<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MaxProperties extends Count implements SchemaPathAwareConstraint
{
    const TOO_MANY_ERROR = Count::TOO_MANY_ERROR;

    const TOO_MANY_MESSAGE = 'This object should contain at most one property'
                            . '|This object should contain at most {{ limit }} properties';

    public string $path;

    public $maxMessage = self::TOO_MANY_MESSAGE;

    public function __construct(int $value, string $path)
    {
        $this->path = "{$path}/maxProperties";
        parent::__construct(
            [
                'max' => $value,
                'groups' => ['Default']
            ]
        );
    }

    public function validatedBy()
    {
        return CountValidator::class;
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
