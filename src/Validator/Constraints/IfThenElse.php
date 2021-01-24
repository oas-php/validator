<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class IfThenElse extends Constraint
{
    public OAS\Schema $ifSchema;

    public ?OAS\Schema $thenSchema;

    public ?OAS\Schema $elseSchema;

    public function __construct(
        OAS\Schema $ifSchema,
        ?OAS\Schema $thenSchema,
        ?OAS\Schema $elseSchema,
        string $path,
        Configuration $configuration
    ) {
        $this->ifSchema = $ifSchema;
        $this->thenSchema = $thenSchema;
        $this->elseSchema = $elseSchema;
        parent::__construct($path, $configuration);
    }
}
