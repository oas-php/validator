<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OneOfValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OneOf) {
            throw new UnexpectedTypeException($constraint, OneOf::class);
        }

        $nestedViolations = [];
        $successfullyEvaluatedPaths = [];
        $context = $this->context;

        foreach ($constraint->schemas as $index => $schema) {
            $schemaConstraint = new Schema(
                $schema,
                configuration: $constraint->configuration
            );
            $violations = $context->getValidator()->validate($value, $schemaConstraint);

            if ($violations->count() > 0) {
                $nestedViolations[$index] = $violations;
            } else {
                $successfullyEvaluatedPaths = array_merge(
                    $successfullyEvaluatedPaths,
                    $schemaConstraint->successfullyEvaluatedPaths->getArrayCopy()
                );
            }
        }

        $matchedSchemas = count($constraint->schemas) - count($nestedViolations);

        if ($matchedSchemas == 0) {
            $this->addViolation(
                $context,
                OneOf::NONE_SCHEMAS_MATCHED_MESSAGE,
                OneOf::NONE_SCHEMAS_MATCHED_ERROR,
                $nestedViolations,
            );
        } elseif ($matchedSchemas == 1) {
            foreach ($successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                $constraint->enclosingSchemaConstraint
                    ->successfullyEvaluatedPaths
                    ->append($successfullyEvaluatedPath);

                $constraint->referencingSchemaConstraint
                    ?->successfullyEvaluatedPaths
                    ->append($successfullyEvaluatedPath);
            }
        } else {
            $matchedSchemaIndexes = array_diff(
                array_keys($constraint->schemas),
                array_keys($nestedViolations)
            );

            $this->addViolation(
                $context,
                OneOf::MANY_SCHEMAS_MATCHED_MESSAGE,
                OneOf::MANY_SCHEMAS_MATCHED_ERROR,
                $nestedViolations,
                ['{{ matched_schema_indexes }}' => join(', ', $matchedSchemaIndexes)],
            );
        }
    }

    private function addViolation(
        ExecutionContextInterface $context,
        string $messageTemplate,
        string $code,
        array $nestedViolations,
        array $templateParameters = []
    ): void
    {
        $constraintViolationBuilder = $context
            ->buildViolation($messageTemplate, $templateParameters)
            ->setCode($code);

        if ($constraintViolationBuilder instanceof Validator\ConstraintViolationBuilder) {
            $constraintViolationBuilder->setNestedConstraintViolations($nestedViolations);
        }

        $constraintViolationBuilder->addViolation();
    }
}
