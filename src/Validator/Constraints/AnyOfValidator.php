<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AnyOfValidator extends ConstraintValidator
{
    public function validate(mixed $instance, Constraint $constraint): void
    {
        if (!$constraint instanceof AnyOf) {
            throw new UnexpectedTypeException($constraint, AnyOf::class);
        }

        $nestedViolations = [];
        $successfullyEvaluatedPaths = [];
        $context = $this->context;

        foreach ($constraint->schemas as $schema) {
            $schemaConstraint = new Schema(
                $schema,
                configuration: $constraint->configuration
            );
            $violations = $context->getValidator()->validate($instance, $schemaConstraint);

            if ($violations->count() > 0) {
                $nestedViolations[] = $violations;
            } else {
                $successfullyEvaluatedPaths = array_merge(
                    $successfullyEvaluatedPaths,
                    $schemaConstraint->successfullyEvaluatedPaths->getArrayCopy()
                );
            }
        }

        if (count($constraint->schemas) == count($nestedViolations)) {
            $constraintBuilder = $context
                ->buildViolation(AnyOf::NONE_SCHEMAS_MATCHED_MESSAGE)
                ->setCode(AnyOf::NONE_SCHEMAS_MATCHED_ERROR);

            if ($constraintBuilder instanceof Validator\ConstraintViolationBuilder) {
                $constraintBuilder->setNestedConstraintViolations($nestedViolations);
            }

            $constraintBuilder->addViolation();
        } else {
            foreach ($successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                $constraint->successfullyEvaluatedPaths->append($successfullyEvaluatedPath);
            }
        }
    }
}
