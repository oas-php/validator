<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Not extends Constraint
{
    const INSTANCE_MATCHES_SCHEMA_ERROR = 'e98f991f-e856-48ab-872b-5ffc60d31cb3';

    const INSTANCE_MATCHES_SCHEMA_MESSAGE = 'Instance is valid against schema';

    public OAS\Schema $schema;

    public function __construct(OAS\Schema $schema, string $path, Configuration $configuration)
    {
        $this->schema = $schema;
        parent::__construct("{$path}/not", $configuration);
    }
}
