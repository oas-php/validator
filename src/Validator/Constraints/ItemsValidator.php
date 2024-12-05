<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use OAS;
use function OAS\Validator\isList;

class ItemsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Items) {
            throw new UnexpectedTypeException($constraint, Items::class);
        }

        if (!isList($value) || empty($value)) {
            return;
        }

        $enclosingSchema = $constraint->enclosingSchemaConstraint->schema;
        assert($enclosingSchema instanceof OAS\Schema);

        $prefixItems = $enclosingSchema->getPrefixItems();

        for ($index = ($prefixItems !== null ? count($prefixItems) : 0); $index < count($value); $index++) {
            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->atPath("[$index]")
                ->validate(
                    $value[$index],
                    new Schema(
                        $constraint->schema,
                        $constraint->getSchemaPath(),
                        $constraint->configuration,
                        $constraint->enclosingSchemaConstraint
                    )
                );
        }
    }
}
