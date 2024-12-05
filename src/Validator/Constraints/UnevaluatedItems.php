<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use ArrayObject;
use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class UnevaluatedItems extends Constraint
{
    const UNEVALUATED_ITEM_ERROR_CODE = 'UNEVALUATED_ITEM';
    const UNEVALUATED_ITEM_ERROR_MESSAGE = 'Item "{{ item }}" has not been successfully evaluated and the schema does not allow unevaluated items.';

    public function __construct(
        public readonly OAS\Schema|bool $schema,
        public readonly Schema $enclosingSchemaConstraint,
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {

        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/unevaluatedItems",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
