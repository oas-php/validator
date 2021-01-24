<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\LessThan;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class ExclusiveMaximum extends LessThan implements SchemaPathAwareConstraint
{
    const TOO_HIGH_ERROR = LessThan::TOO_HIGH_ERROR;

    const TOO_HIGH_MESSAGE = 'Value {{ value }} is greater or equal to maximum value of {{ compared_value }}';

    /** @var float|int  */
    public $value;

    public string $path;

    public $message = self::TOO_HIGH_MESSAGE;

    /**
     * @param float|int $value
     * @param string    $path
     */
    public function __construct($value, string $path)
    {
        $this->value = $value;
        $this->path = "{$path}/exclusiveMaximum";
        parent::__construct(['groups' => ['Default']]);
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
