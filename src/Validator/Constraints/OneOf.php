<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class OneOf extends Constraint
{
    const NONE_SCHEMAS_MATCHED_ERROR = '70cc380b-83f4-481e-9b53-b4b5dc22ccc6';

    const NONE_SCHEMAS_MATCHED_MESSAGE = 'Instance must be valid against exactly one schema but is not valid against any';

    const MANY_SCHEMAS_MATCHED_ERROR = 'de84b240-d7f3-43b2-b44a-ac3f749c9763';

    const MANY_SCHEMAS_MATCHED_MESSAGE = 'Instance must be valid against exactly one schema but is valid against many';

    /** @var OAS\Schema[] */
    public array $schemas;

    public function __construct(array $schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("$path/oneOf", $configuration);
    }
}
