<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MinContains extends Constraint
{
    const TOO_FEW_ERROR = 'b5eab9ab-df61-4dab-bd36-4517d82e4745';

    const TOO_FEW_MESSAGE = 'This collection should contain at least one item which validates against schema'
                            . '|This collection should contain at least {{ min }} items which validates against schema';

    public OAS\Schema $schema;

    public int $min;

    public function __construct(OAS\Schema $schema, int $min, string $path, Configuration $configuration)
    {
        $this->schema = $schema;
        $this->min = $min;
        parent::__construct("{$path}/minContains", $configuration);
    }
}
