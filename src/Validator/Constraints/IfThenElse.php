<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class IfThenElse extends Constraint
{
    public function __construct(
        public readonly OAS\Schema|bool $ifSchema,
        public readonly OAS\Schema|bool|null $thenSchema,
        public readonly OAS\Schema|bool|null $elseSchema,
        public readonly Schema $enclosingSchemaConstraint,
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
