<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class PatternProperties extends Constraint
{
    /** @var OAS\Schema[] */
    public array $schemas;

    public function __construct(array $schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("{$path}/patternProperties", $configuration);
    }
}
