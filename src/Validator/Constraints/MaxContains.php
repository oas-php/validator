<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class MaxContains extends Constraint
{
    const TOO_MANY_ERROR = '72b25dda-d5de-4db7-90a8-0244aaac2463';

    const TOO_MANY_MESSAGE = 'This collection should contain at most one item which validates against schema|'
                            . 'This collection should contain at most {{ max }} items which validates against schema';

    public OAS\Schema $schema;

    public int $max;

    public function __construct(OAS\Schema $schema, int $max, string $path, Configuration $configuration)
    {
        $this->schema = $schema;
        $this->max = $max;
        parent::__construct("{$path}/maxContains", $configuration);
    }
}
