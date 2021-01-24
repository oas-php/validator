<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Not) {
            throw new UnexpectedTypeException($constraint, Not::class);
        }

        $schema = new Schema($constraint->schema, '#', $constraint->configuration);

        $violations = $this->context->getValidator()->validate($value, $schema);

        if (count($violations) == 0) {
            $this->context
                ->buildViolation(Not::INSTANCE_MATCHES_SCHEMA_MESSAGE)
                ->setCode(Not::INSTANCE_MATCHES_SCHEMA_ERROR)
                ->addViolation();
        }
    }
}
