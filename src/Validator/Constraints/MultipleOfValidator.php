<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isNumber;

class MultipleOfValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MultipleOf) {
            throw new UnexpectedTypeException($constraint, MultipleOf::class);
        }

        if (!isNumber($value)) {
            return;
        }

        $value = (float) $value;
        $multipleOf = (float) $constraint->multipleOf;

        if ((0.0 == $multipleOf && 0.0 != $value) || (0.0 != $multipleOf && 0.0 != $this->mod($value, $multipleOf, $constraint))) {
            $this->context->buildViolation(MultipleOf::NOT_MULTIPLE_OF_MESSAGE)
                ->setCode(MultipleOf::NOT_MULTIPLE_OF_ERROR)
                ->setParameter('{{ value }}', (string) $value)
                ->setParameter('{{ multiple_of }}', (string) $constraint->multipleOf)
                ->addViolation();
        }
    }

    private function mod(float $x, float $y, MultipleOf $constraint): float
    {
        if (extension_loaded('bcmath')) {
            bcscale($constraint->scale);
            $pattern  = "%.{$constraint->scale}f";

            return (float) bcmod(
                sprintf($pattern, $x),
                sprintf($pattern, $y)
            );
        }

        $div = (int) ($x / $y);

        return (float) ($x - ($div * $y));
    }
}
