<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Maximum extends LessThanOrEqual implements SchemaPathAwareConstraint
{
    const TOO_HIGH_ERROR = 'a1782b1f-7c98-4f2e-99fa-79174e623c39';
    const TOO_HIGH_MESSAGE = 'Value {{ value }} exceeds maximum value of {{ compared_value }}';

    public string $path;
    public $message = self::TOO_HIGH_MESSAGE;

    /**
     * @param float|int $value
     */
    public function __construct(int|float $value, string $path)
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
