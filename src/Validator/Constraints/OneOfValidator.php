<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OneOfValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OneOf) {
            throw new UnexpectedTypeException($constraint, OneOf::class);
        }

        $nestedViolations = [];
        $context = $this->context;

        foreach ($constraint->schemas as $index => $schema) {
            $schemaConstraint = new Schema($schema, '#', $constraint->configuration);
            $violations = $context->getValidator()->validate($value, $schemaConstraint);

            if ($violations->count() > 0) {
                $nestedViolations[$index] = $violations;
            }
        }

        $matchedSchemas = count($constraint->schemas) - count($nestedViolations);

        if (0 == $matchedSchemas) {
            $this->addViolation(
                $context,
                OneOf::NONE_SCHEMAS_MATCHED_MESSAGE,
                OneOf::NONE_SCHEMAS_MATCHED_ERROR,
                $nestedViolations,
            );
        }

        if ($matchedSchemas > 1) {
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
