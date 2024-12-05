<?php

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BooleanValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Boolean) {
            throw new UnexpectedTypeException($constraint, Boolean::class);
        }

        if (!$constraint->value) {
            $this->context
                ->buildViolation($constraint::ALWAYS_INVALID_ERROR_MESSAGE)
                ->setCode($constraint::ALWAYS_INVALID_ERROR_CODE)
                ->addViolation();
        }
    }
}