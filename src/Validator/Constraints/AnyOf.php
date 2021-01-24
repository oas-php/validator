<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class AnyOf extends Constraint
{
    const NONE_SCHEMAS_MATCHED_ERROR = 'ce9ad211-b5f3-4c49-a4b3-946553f006c8';

    const NONE_SCHEMAS_MATCHED_MESSAGE = 'Instance is not valid against any schema';

    /** @var OAS\Schema[] */
    public array $schemas;

    public function __construct(array $schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("$path/anyOf", $configuration);
    }
}
