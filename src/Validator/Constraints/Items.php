<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Items extends Constraint
{
    public function __construct(
        public readonly OAS\Schema|bool $schema,
        public readonly Schema $enclosingSchemaConstraint
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/items",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
