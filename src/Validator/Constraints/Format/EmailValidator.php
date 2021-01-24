<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\EmailValidator as Base;

class EmailValidator extends Base
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === '') {
            $this->context
                ->buildViolation(Email::INVALID_FORMAT_MESSAGE)
                ->setCode(Email::INVALID_FORMAT_ERROR)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        parent::validate($value, $constraint);
    }
}
