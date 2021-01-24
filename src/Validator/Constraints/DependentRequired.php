<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class DependentRequired extends Constraint
{
    const DEPENDENT_PROPERTIES_MISSING_ERROR = '767e8060-ad50-418e-a84c-648883aae756';

    const DEPENDENT_PROPERTIES_MISSING_MESSAGE = 'Properties {{ missing_properties }} required by {{ property }} property are missing';

    /** @var string[][] */
    public array $dependencies;

    public function __construct(array $dependencies, string $path)
    {
        $this->dependencies = $dependencies;
        parent::__construct("{$path}/dependentRequired");
    }
}
