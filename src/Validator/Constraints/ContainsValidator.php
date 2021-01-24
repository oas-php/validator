<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class ContainsValidator extends ConstraintValidator
{
    public function validate($items, Constraint $constraint)
    {
        if (!$constraint instanceof Contains) {
            throw new UnexpectedTypeException($constraint, Contains::class);
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

            if ($violations->count() === 0) {
                return;
            }

            $nestedViolations[] = $violations;
        }

        $constraintViolationBuilder = $context
            ->buildViolation(Contains::NO_ITEM_MATCHES_MESSAGE)
            ->setCode(Contains::NO_ITEM_MATCHES_ERROR);

        if ($constraintViolationBuilder instanceof Validator\ConstraintViolationBuilder) {
            $constraintViolationBuilder->setNestedConstraintViolations($nestedViolations);
        }

        $constraintViolationBuilder->addViolation();
    }
}
