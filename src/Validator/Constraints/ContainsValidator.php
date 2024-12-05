<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class ContainsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Contains) {
            throw new UnexpectedTypeException($constraint, Contains::class);
        }

        if (!isList($value)) {
            return;
        }

        $isValid = false;
        $nestedViolations = [];
        $context = $this->context;
        $containsSchema = new Schema(
            $constraint->schema,
            '#',
            $constraint->configuration,
            $constraint->enclosingSchemaConstraint,
            $constraint->referencingSchemaConstraint
        );

        foreach ($value as $key => $item) {
            $violations = $context
                ->getValidator()
                ->startContext()
                ->atPath("[$key]")
                ->validate($item, $containsSchema)
                ->getViolations();

            if ($violations->count() == 0) {
                $isValid = true;
            } else {
                $nestedViolations[] = $violations;
            }
        }

        if (!$isValid) {
            $constraintViolationBuilder = $context->buildViolation(Contains::NO_ITEM_MATCHES_MESSAGE);
            assert($constraintViolationBuilder instanceof Validator\ConstraintViolationBuilder);

            $constraintViolationBuilder
                ->setCode(Contains::NO_ITEM_MATCHES_ERROR)
                ->setNestedConstraintViolations($nestedViolations)
                ->addViolation();
        }
    }
}
