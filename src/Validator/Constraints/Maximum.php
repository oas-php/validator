<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Maximum extends LessThanOrEqual implements SchemaPathAwareConstraint
{
    const TOO_HIGH_ERROR = LessThanOrEqual::TOO_HIGH_ERROR;

    const TOO_HIGH_MESSAGE = 'Value {{ value }} exceeds maximum value of {{ compared_value }}';

    public $value;

    public string $path;

    public $message = self::TOO_HIGH_MESSAGE;

    /**
     * @param float|int $value
     * @param string    $path
     */
    public function __construct($value, string $path)
    {
        $this->path = "{$path}/maximum";
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
