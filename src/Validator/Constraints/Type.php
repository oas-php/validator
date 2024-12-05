<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Schema;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in \OAS\Validator\Constraints\Schema
 */
class Type extends Constraint
{
    const INVALID_TYPE_ERROR = '38d57100-784b-4b0a-8196-4045efa17f98';
    const INVALID_TYPE_MESSAGE = 'Invalid type: expected "{{ expected }}" but got "{{ actual }}"';

    /** @var Schema\Type|array<int,Schema\Type > $type */
    public Schema\Type|array $type;

    public function __construct(Schema\Type|array $type, string $path)
    {
        $this->type = $type;
        parent::__construct("{$path}/type");
    }
}
