<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MaxItems extends Count implements SchemaPathAwareConstraint
{
    const TOO_MANY_ERROR = Count::TOO_MANY_ERROR;

    const TOO_MANY_MESSAGE = 'This collection should contain at most one item'
                            . 'This collection should contain at most {{ limit }} items';

    public string $path;

    public $maxMessage = self::TOO_MANY_MESSAGE;

    public function __construct(int $value, string $path)
    {
        $this->max = $value;
        $this->path = "{$path}/maxItems";
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
