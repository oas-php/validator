<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Constant extends Constraint
{
    const VALUE_MISMATCH_ERROR = 'a357fb30-c9b7-43f8-b82c-13fcfbe9b8c2';

    const VALUE_MISMATCH_MESSAGE = 'Value does not match the constant';

    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     * @param string $path
     */
    public function __construct($value, string $path)
    {
        $this->value = $value;
        parent::__construct("{$path}/const");
    }
}
