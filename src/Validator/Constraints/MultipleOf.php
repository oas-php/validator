<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class MultipleOf extends Constraint
{
    const NOT_MULTIPLE_OF_ERROR = '31c29b3d-9a9e-4f88-88dc-23cf141a5bde';

    const NOT_MULTIPLE_OF_MESSAGE = 'Value {{ value }} is not a multiple of {{ multiple_of }}';

    /** @var int|float */
    public $multipleOf;

    public int $scale;

    public function __construct($multipleOf, string $path, int $scale)
    {
        $this->multipleOf = $multipleOf;
        $this->scale = $scale;
        parent::__construct("{$path}/multipleOf");
    }
}
