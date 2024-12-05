<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MaxContains extends Constraint
{
    const TOO_MANY_ERROR = '72b25dda-d5de-4db7-90a8-0244aaac2463';
    const TOO_MANY_MESSAGE = 'This collection should contain at most one item which validates against schema|'
                            . 'This collection should contain at most {{ max }} items which validates against schema';

    public function __construct(
        public readonly OAS\Schema $schema,
        public readonly int $max,
        public readonly Schema $enclosingSchemaConstraint
    ) {
        parent::__construct(
            "{$this->enclosingSchemaConstraint->schemaPath}/maxContains",
            $this->enclosingSchemaConstraint->configuration
        );
    }
}
