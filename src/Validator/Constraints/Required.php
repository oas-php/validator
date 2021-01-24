<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\SchemaPathAwareConstraint;
use Symfony\Component\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Required extends Constraint implements SchemaPathAwareConstraint
{
    const REQUIRED_PROPERTIES_MISSING_ERROR = '8a5fe94e-bbcb-4568-9d15-5c12304008cd';

    const REQUIRED_PROPERTIES_MISSING_MESSAGE = 'Required property {{ required_properties }} is missing'
                                                . '|Required properties {{ required_properties }} are missing';

    /** @var string[] */
    public array $properties;

    public string $path;

    public function __construct(array $properties, string $path)
    {
        $this->properties = $properties;
        $this->path = "$path/required";
    }

    public function getSchemaPath(): string
    {
        return $this->path;
    }
}
