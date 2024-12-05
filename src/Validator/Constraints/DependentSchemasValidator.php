<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class DependentSchemasValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DependentSchemas) {
            throw new UnexpectedTypeException($constraint, DependentSchemas::class);
        }

        if (!isObject($value)) {
            return;
        }

        $value = (array) $value;

        foreach ($constraint->dependentSchemas as $key => $dependentSchema) {
            if (array_key_exists($key, $value)) {
                $dependentSchemaConstraint = new Schema(
                    $dependentSchema,
                    "{$constraint->getSchemaPath()}/{$key}",
                    $constraint->configuration
                );

                $violations = $this->context
                    ->getValidator()
                    ->inContext($this->context)
                    ->validate($value, $dependentSchemaConstraint)
                    ->getViolations();

                if ($violations->count() == 0) {
                    foreach ($dependentSchemaConstraint->successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                        $constraint
                            ->enclosingSchemaConstraint
                            ->successfullyEvaluatedPaths
                            ->append($successfullyEvaluatedPath);
                    }
                }
            }
        }
    }
}
