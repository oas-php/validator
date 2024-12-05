<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Items2019_09 extends Constraint
{
    /** @var \OAS\Schema|array<int, \OAS\Schema> */
    public OAS\Schema|array $schemas;

    public function __construct(OAS\Schema|array $schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("$path/items", $configuration);
    }
}
