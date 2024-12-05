<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class UnevaluatedItemsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UnevaluatedItems) {
            throw new UnexpectedTypeException($constraint, UnevaluatedItems::class);
        }

        if (!isList($value) || empty($value)) {
            return;
        }

        $successfullyEvaluatedPaths = $constraint
            ->enclosingSchemaConstraint
            ->successfullyEvaluatedPaths
            ->getArrayCopy();

        foreach ($value as $key => $item) {
            if (!in_array($key, $successfullyEvaluatedPaths)) {
                if ($constraint->schema === false) {
                    $this->context
                        ->buildViolation(UnevaluatedItems::UNEVALUATED_ITEM_ERROR_MESSAGE)
                        ->setParameter('{{ item }}', (string) $key)
                        ->setCode(UnevaluatedItems::UNEVALUATED_ITEM_ERROR_CODE)
                        ->atPath("[$key]")
                        ->setInvalidValue($value)
                        ->addViolation();
                } else {
                    $this->context
                        ->getValidator()
                        ->inContext($this->context)
                        ->atPath("[$key]")
                        ->validate(
                            $item,
                            new Schema(
                                $constraint->schema,
                                $constraint->getSchemaPath(),
                                $constraint->configuration,
                                $constraint->enclosingSchemaConstraint,
                                $constraint->referencingSchemaConstraint
                            )
                        );
                }
            }
        }
    }
}
