<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class UniqueItems extends Constraint
{
    const NOT_UNIQUE_ERROR = '39234473-73e7-4667-8b1e-7c0d9e94e656';

    const NOT_UNIQUE_MESSAGE = 'Items are not unique';

    public function __construct(string $path)
    {
        parent::__construct("$path/uniqueItems");
    }
}
