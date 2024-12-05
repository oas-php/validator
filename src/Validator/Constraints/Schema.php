<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use ArrayObject;
use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

class Schema extends Constraint
{
    public ArrayObject $successfullyEvaluatedPaths;

    public function __construct(
        public readonly OAS\Schema|bool $schema,
        // TODO: shouldn't it be "#/"
        string $path = '#',
        Configuration $configuration = null,
        public readonly ?Schema  $enclosingSchemaConstraint = null,
        // TODO: rename to enclosingSchemaConstraint?
        public readonly ?Schema $referencingSchemaConstraint = null
    ) {
        $this->successfullyEvaluatedPaths = new ArrayObject();
        parent::__construct($path, $configuration ?? new Configuration());
    }
}
