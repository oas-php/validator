<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\HostnameValidator as Base;

class HostnameValidator extends Base
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === '') {
            $this->context
                ->buildViolation(Hostname::INVALID_HOSTNAME_MESSAGE)
                ->setCode(Hostname::INVALID_HOSTNAME_ERROR)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        parent::validate($value, $constraint);
    }
}
