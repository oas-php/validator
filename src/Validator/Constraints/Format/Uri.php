<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;

class Uri extends Constraint
{
    const INVALID_URI_ERROR = '42f83b32-dcdb-41b7-9f0e-ff2443ae6c18';

    const INVALID_URI_MESSAGE = 'This value {{ value }} is not a valid URI';
}
