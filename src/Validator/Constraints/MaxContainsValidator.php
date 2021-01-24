<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class MaxContainsValidator extends ConstraintValidator
{
    public function validate($items, Constraint $constraint)
    {
        if (!$constraint instanceof MaxContains) {
            throw new UnexpectedTypeException($constraint, MaxContains::class);
        }

        if (!isList($items)) {
            return;
        }

        $nestedViolations = [];
        $context = $this->context;
        $containsSchema = new Schema(
            $constraint->schema, '#', $constraint->configuration
        );

        foreach ($items as $item) {
            $violations = $context->getValidator()->validate($item, $containsSchema);

            if ($violations->count() > 0) {
                $nestedViolations[] = $violations;
            }
        }

        $validCount = count($items) - count($nestedViolations);

        if ($validCount > $constraint->max) {
            $constraintViolationBuilder = $context
                ->buildViolation(MaxContains::TOO_MANY_MESSAGE)
                ->setPlural($constraint->max)
                ->setCode(MaxContains::TOO_MANY_ERROR)
                ->setParameter('{{ max }}', (string) $constraint->max);

            if ($constraintViolationBuilder instanceof Validator\ConstraintViolationBuilder) {
                $constraintViolationBuilder->setNestedConstraintViolations($nestedViolations);
            }

            $constraintViolationBuilder->addViolation();
        }
    }
}
