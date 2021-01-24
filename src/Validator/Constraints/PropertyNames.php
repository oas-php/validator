<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

class PropertyNames extends Constraint
{
    public OAS\Schema $schema;

    public string $path;

    public function __construct(OAS\Schema $schema, string $path, Configuration $configuration)
    {
        $this->schema = $schema;
        parent::__construct("{$path}/propertyNames", $configuration);
    }
}
