<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class DependentSchemas extends Constraint
{
    /** @param  array<string, \OAS\Schema> $dependentSchemas */
    public function __construct(
        public readonly array $dependentSchemas,
        string $path,
        Configuration $configuration,
        public readonly Schema $enclosingSchemaConstraint
    ) {
        parent::__construct("$path/dependentSchemas", $configuration);
    }
}
