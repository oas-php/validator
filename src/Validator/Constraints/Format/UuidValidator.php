<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\UuidValidator as Base;

class UuidValidator extends Base
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === '') {
            $this->context
                ->buildViolation(Uuid::INVALID_UUID_MESSAGE)
                ->setCode(Uuid::INVALID_UUID_ERROR)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        parent::validate($value, $constraint);
    }
}
