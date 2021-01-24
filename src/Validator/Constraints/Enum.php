<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Enum extends Constraint
{
    const INVALID_CHOICE_ERROR = 'eed5ba60-d464-447d-8e4a-609d1e00ed0a';

    const INVALID_CHOICE_MESSAGE = 'Value "{{ value }}" is not a valid choice (valid choices are: {{ choices }})';

    public array $choices;

    public function __construct(array $choices, string $path)
    {
        $this->choices = $choices;
        parent::__construct("{$path}/enum");
    }
}
