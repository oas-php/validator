<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class PropertyNamesValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof PropertyNames) {
            throw new UnexpectedTypeException($constraint, PropertyNames::class);
        }

        if (!isObject($instance)) {
            return;
        }
        $context = $this->context;
        $propertyNames = array_keys((array) $instance);
        $schemaConstraint = new Schema(
            $constraint->schema, $constraint->getSchemaPath(), $constraint->configuration
        );

        foreach ($propertyNames as $propertyName) {
            $context
                ->getValidator()
                ->inContext($context)
                ->validate($propertyName, $schemaConstraint);
        }
    }
}
