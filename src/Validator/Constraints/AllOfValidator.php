<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\ConstraintViolationBuilder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AllOfValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof AllOf) {
            throw new UnexpectedTypeException($constraint, AllOf::class);
        }

        $nestedViolations = [];
        $context = $this->context;

        foreach ($constraint->schemas as $index => $schema) {
            $schemaConstraint = new Schema($schema, '#', $constraint->configuration);
            $violations =  $context->getValidator()->validate($instance, $schemaConstraint);

            if ($violations->count() > 0) {
                $nestedViolations[$index] = $violations;
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
        }
    }
}
