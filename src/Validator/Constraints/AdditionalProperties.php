<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Schema;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class AdditionalProperties extends Constraint
{
    const UNEXPECTED_PROPERTY_ERROR = '577f6036-de73-45f8-a819-99e1d0341e58';

    const UNEXPECTED_PROPERTY_MESSAGE = 'Property "{{ property }}" has not been defined and additional properties are not allowed';

    public Schema $schema;

    public array $knownPropertyNames;

    public array $knownPropertyPatterns;

    public function __construct(
        Schema $schema,
        array $knownPropertyNames,
        array $knownPropertyPatterns,
        string $path,
        Configuration $configuration
    ) {
        $this->schema = $schema;
        $this->knownPropertyNames = $knownPropertyNames;
        $this->knownPropertyPatterns = $knownPropertyPatterns;
        parent::__construct("{$path}/additionalProperties", $configuration);
    }
}
