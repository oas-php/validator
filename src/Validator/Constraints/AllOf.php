<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class AllOf extends Constraint
{
    const NOT_ALL_SCHEMAS_MATCHED_ERROR = 'efac181b-d5d4-42fa-9827-0c2e619bb161';

    const NOT_ALL_SCHEMAS_MATCHED_MESSAGE = 'Instance is not valid against all schemas';

    /** @var \OAS\Schema[] */
    public array $schemas;

    public function __construct(array $schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("$path/allOf", $configuration);
    }
}
