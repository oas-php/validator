<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class DependentSchemas extends Constraint
{
    /** @var OAS\Schema[] */
    public array $dependentSchemas;

    public function __construct(array $dependentSchemas, string $path, Configuration $configuration)
    {
        $this->dependentSchemas = $dependentSchemas;
        parent::__construct("{$path}/dependentSchemas", $configuration);
    }
}
