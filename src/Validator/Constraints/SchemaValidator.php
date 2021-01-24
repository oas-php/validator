<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchemaValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Schema) {
            throw new UnexpectedTypeException($constraint, Schema::class);
        }

        $schema = $constraint->schema;

        if ($schema->isAlwaysInvalid()) {
            $this->context
                ->buildViolation(Schema::ALWAYS_INVALID_MESSAGE)
                ->setCode(Schema::ALWAYS_INVALID_ERROR)
                ->addViolation();

            return;
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, $constraint->buildConstraint());
    }
}
