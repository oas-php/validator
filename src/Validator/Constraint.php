<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\Constraint as BaseConstraint;

class Constraint extends BaseConstraint implements SchemaPathAwareConstraint
{
    public function __construct(
        public readonly string $schemaPath,
        public readonly ?Configuration $configuration = null
    ) {
        parent::__construct();
    }

    public function getSchemaPath(): string
    {
        return $this->schemaPath;
    }
}
