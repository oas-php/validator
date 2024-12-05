<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Contains extends Constraint
{
    const NO_ITEM_MATCHES_ERROR = 'b6771602-4c49-4047-86bf-8f51817a4d3d';
    const NO_ITEM_MATCHES_MESSAGE = 'No item is valid against schema';

    public function __construct(
        public readonly OAS\Schema|bool $schema,
        public readonly Schema $enclosingSchemaConstraint,
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/contains",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
