<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DurationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        try {
            new \DateInterval($value);
        } catch (\Throwable $e) {
            $this->context
                ->buildViolation(Duration::INVALID_DURATION_MESSAGE)
                ->setCode(Duration::INVALID_DURATION_ERROR)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();

            return;
        }
    }
}
