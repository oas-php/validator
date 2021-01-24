<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Minimum extends GreaterThanOrEqual implements SchemaPathAwareConstraint
{
    const TOO_LOW_ERROR = 'b1ebf893-f108-4d6e-b725-06994c6ba7ab';

    const TOO_LOW_MESSAGE = 'Value {{ value }} is less than minimum value of {{ compared_value }}';

    public string $path;

    public $message = self::TOO_LOW_MESSAGE;

    /**
     * @param float|int $value
     * @param string $path
     */
    public function __construct($value, string $path)
    {
        $this->path = "{$path}/minimum";
        parent::__construct(
            [
                'value' => $value,
                'groups' => ['Default']
            ]
        );
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
