<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;

class Duration extends Constraint
{
    const INVALID_DURATION_ERROR = '791535ee-fbc4-478a-913b-01542c94f9d4';

    const INVALID_DURATION_MESSAGE = 'This value {{ value }} is not a valid duration';
}
