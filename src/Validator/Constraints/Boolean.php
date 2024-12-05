<?php

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

class Boolean extends Constraint
{
    const ALWAYS_INVALID_ERROR_CODE = 'ALWAYS_INVALID';
    const ALWAYS_INVALID_ERROR_MESSAGE = 'Schema always fails validation';

    public function __construct(
        public readonly bool $value,
        public readonly Schema $enclosingSchemaConstraint,
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {
        parent::__construct(
            $this->enclosingSchemaConstraint->getSchemaPath(),
            $this->enclosingSchemaConstraint->configuration
        );
    }
}