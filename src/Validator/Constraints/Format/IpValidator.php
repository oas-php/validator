<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\IpValidator as Base;

class IpValidator extends Base
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === '') {
            $this->context
                ->buildViolation(Ip::INVALID_IP_MESSAGE)
                ->setCode(Ip::INVALID_IP_MESSAGE)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        parent::validate($value, $constraint);
    }
}
