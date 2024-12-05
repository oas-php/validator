<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class PrefixItems extends Constraint
{
    /** @param array<int, \OAS\Schema|bool> $schemas */
    public function __construct(
        public readonly array $schemas,
        public readonly Schema $enclosingSchemaConstraint,
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/prefixItems",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
