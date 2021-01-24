<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class ExclusiveMinimum extends GreaterThan implements SchemaPathAwareConstraint
{
    const TOO_LOW_ERROR = GreaterThan::TOO_LOW_ERROR;

    const TOO_LOW_MESSAGE = 'Value {{ value }} is less or equal to minimum value of {{ compared_value }}';

    /** @var float|int */
    public $value;

    public string $path;

    public $message = self::TOO_LOW_MESSAGE;

    /**
     * @param float|int $value
     * @param string $path
     */
    public function __construct($value, string $path)
    {
        $this->value = $value;
        $this->path = "$path/exclusiveMinimum";
        parent::__construct(['groups' => ['Default']]);
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
