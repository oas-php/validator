<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Contains extends Constraint
{
    const NO_ITEM_MATCHES_ERROR = 'b6771602-4c49-4047-86bf-8f51817a4d3d';

    const NO_ITEM_MATCHES_MESSAGE = 'No item is valid against schema';

    public OAS\Schema $schema;

    public function __construct(OAS\Schema $schema, string $path, Configuration $configuration)
    {
        $this->schema = $schema;
        parent::__construct("{$path}/contains", $configuration);
    }
}
