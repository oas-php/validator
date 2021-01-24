<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\DateTimeValidator as Base;

class DateTimeValidator extends Base
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === '') {
            $this->context
                ->buildViolation(DateTime::INVALID_FORMAT_MESSAGE)
                ->setCode(DateTime::INVALID_FORMAT_ERROR)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        parent::validate(
            $this->normalize($value), $constraint
        );
    }

    private function normalize(string $value): string
    {
        return strtoupper($value);
    }
}
