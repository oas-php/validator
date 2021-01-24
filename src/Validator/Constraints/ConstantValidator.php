<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\{normalize, equal};

class ConstantValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Constant) {
            throw new UnexpectedTypeException($constraint, Constant::class);
        }

        $equal = equal(
            normalize($value),
            normalize($constraint->value)
        );

        if (!$equal) {
            $this->context
                ->buildViolation(Constant::VALUE_MISMATCH_MESSAGE)
                ->setCode(Constant::VALUE_MISMATCH_ERROR)
                ->addViolation();
        }
    }
}
