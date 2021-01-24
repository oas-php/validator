<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IfThenElseValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IfThenElse) {
            throw new UnexpectedTypeException($constraint, IfThenElse::class);
        }

        $ifSchema = new Schema($constraint->ifSchema, '#', $constraint->configuration);

        $violations = $this->context->getValidator()->validate($value, $ifSchema);

        if (count($violations) == 0 && $constraint->thenSchema instanceof OAS\Schema) {
            $thenSchema = new Schema(
                $constraint->thenSchema, $constraint->getSchemaPath() . '/then', $constraint->configuration
            );

            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->validate($value, $thenSchema);
        }

        if (count($violations) > 0 && $constraint->elseSchema instanceof OAS\Schema) {
            $elseSchema = new Schema(
                $constraint->elseSchema, $constraint->getSchemaPath() . '/else', $constraint->configuration
            );

            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->validate($value, $elseSchema);
        }
    }
}
