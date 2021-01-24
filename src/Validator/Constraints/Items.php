<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Items extends Constraint
{
    /** @var \OAS\Schema|\OAS\Schema[] */
    public $schemas;

    public function __construct($schemas, string $path, Configuration $configuration)
    {
        $this->schemas = $schemas;
        parent::__construct("$path/items", $configuration);
    }
}
