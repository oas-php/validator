<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MinProperties extends Count implements SchemaPathAwareConstraint
{
    const TOO_FEW_ERROR = Count::TOO_FEW_ERROR;

    const TOO_FEW_MESSAGE = 'This object should contain at least one property'
                            . '|This object should contain at least {{ limit }} properties';

    public string $path;

    public $minMessage = self::TOO_FEW_MESSAGE;

    public function __construct(int $value, string $path)
    {
        $this->path = "{$path}/minProperties";
        parent::__construct(
            [
                'min' => $value,
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
