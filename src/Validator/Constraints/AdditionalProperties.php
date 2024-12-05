<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class AdditionalProperties extends Constraint
{
    // TODO: rename to ADDITIONAL_PROPERTY_ERROR?
    // in the end if we provide additionalProperties property we expect some undefined (additional) properties, right?
    const UNEXPECTED_PROPERTY_ERROR = '577f6036-de73-45f8-a819-99e1d0341e58';
    const UNEXPECTED_PROPERTY_MESSAGE = 'Property "{{ property }}" has not been defined and additional properties are not allowed';

    public function __construct(
        public readonly OAS\Schema|bool $schema,
        public readonly Schema $enclosingSchemaConstraint
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/additionalProperties",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
