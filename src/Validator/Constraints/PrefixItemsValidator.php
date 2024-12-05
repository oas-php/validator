<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class PrefixItemsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PrefixItems) {
            throw new UnexpectedTypeException($constraint, PrefixItems::class);
        }

        if (!isList($value) || empty($value)) {
            return;
        }

        foreach ($constraint->schemas as $index => $schema) {
            if (!array_key_exists($index, $value)) {
                break;
            }

            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->atPath("[$index]")
                ->validate(
                    $value[$index],
                    new Schema(
                        $schema,
                        $constraint->getSchemaPath(),
                        $constraint->configuration,
                        $constraint->enclosingSchemaConstraint,
                        $constraint->referencingSchemaConstraint
                    )
                );
        }
    }
}
