<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class DependentSchemasValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof DependentSchemas) {
            throw new UnexpectedTypeException($constraint, DependentSchemas::class);
        }

        if (!isObject($instance)) {
            return;
        }

        $instance = (array) $instance;

        foreach ($constraint->dependentSchemas as $key => $dependentSchema) {
            if (array_key_exists($key, $instance)) {
                $schema = new Schema(
                    $dependentSchema, "{$constraint->getSchemaPath()}/{$key}", $constraint->configuration
                );

                $this->context
                    ->getValidator()
                    ->inContext($this->context)
                    ->validate($instance, $schema);
            }
        }
    }
}
