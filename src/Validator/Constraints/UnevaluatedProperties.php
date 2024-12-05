<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use ArrayObject;
use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class UnevaluatedProperties extends Constraint
{
    const UNEVALUATED_PROPERTY_ERROR = '389fcc7f-f301-466d-8ae7-b9211e500125';
    const UNEVALUATED_PROPERTY_MESSAGE = 'Property "{{ property }}" has not been successfully evaluated and the schema does not allow unevaluated properties.';

    public readonly OAS\Schema|bool $schema;

    public function __construct(
        OAS\Schema|bool $schema,
        string $path,
        Configuration $configuration,
        public ArrayObject $successfullyEvaluatedPaths,
        public readonly Schema $parentSchemaConstraint
    ) {
        $this->schema = $schema;
        parent::__construct("{$path}/unevaluatedProperties", $configuration);
    }
}
