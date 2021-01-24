<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MinItems extends Count implements SchemaPathAwareConstraint
{
    const TOO_FEW_ERROR = Count::TOO_FEW_ERROR;

    const TOO_FEW_MESSAGE = 'This collection should contain at least one item'
                            . '|This collection should contain at least {{ limit }} items';

    public string $path;

    public $minMessage = self::TOO_FEW_MESSAGE;

    public function __construct(int $value, string $path)
    {
        $this->path = "{$path}/minItems";
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
