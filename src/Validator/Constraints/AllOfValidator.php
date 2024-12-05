<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\ConstraintViolationBuilder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AllOfValidator extends ConstraintValidator
{
    public function validate(mixed $instance, Constraint $constraint): void
    {
        if (!$constraint instanceof AllOf) {
            throw new UnexpectedTypeException($constraint, AllOf::class);
        }

        $nestedViolations = [];
        $successfullyEvaluatedPaths = [];
        $context = $this->context;

        foreach ($constraint->schemas as $index => $schema) {
            $schemaConstraint = new Schema(
                $schema,
                '#',
                $constraint->configuration
            );
            $violations =  $context->getValidator()->validate($instance, $schemaConstraint);

            if ($violations->count() > 0) {
                $nestedViolations[$index] = $violations;
            } else {
                $successfullyEvaluatedPaths = array_merge(
                    $successfullyEvaluatedPaths,
                    $schemaConstraint->successfullyEvaluatedPaths->getArrayCopy()
                );
            }
        }

        if (!empty($nestedViolations)) {
            $notPassedSchemasIndexes = array_keys($nestedViolations);

            $constraintViolationBuilder = $context
                ->buildViolation(AllOf::NOT_ALL_SCHEMAS_MATCHED_MESSAGE)
                ->setParameter('{{ not_matched_schema_indexes }}', join(', ', $notPassedSchemasIndexes))
                ->setCode(AllOf::NOT_ALL_SCHEMAS_MATCHED_ERROR);

            if ($constraintViolationBuilder instanceof ConstraintViolationBuilder) {
                $constraintViolationBuilder->setNestedConstraintViolations(
                    array_values($nestedViolations)
                );
            }

            $constraintViolationBuilder->addViolation();
        } else {
            foreach ($successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                $constraint->successfullyEvaluatedPaths->append($successfullyEvaluatedPath);
            }
        }
    }
}
