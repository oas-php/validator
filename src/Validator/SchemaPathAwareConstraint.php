<?php declare(strict_types=1);

namespace OAS\Validator;

interface SchemaPathAwareConstraint
{
    public function getSchemaPath(): string;
}
