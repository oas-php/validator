<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\Constraint as BaseConstraint;

class Constraint extends BaseConstraint implements SchemaPathAwareConstraint
{
    private string $schemaPath;
    public ?Configuration $configuration;

    public function __construct(string $path, Configuration $configuration = null)
    {
        $this->schemaPath = $path;
        $this->configuration = $configuration;
        parent::__construct();
    }

    public function getSchemaPath(): string
    {
        return $this->schemaPath;
    }
}
