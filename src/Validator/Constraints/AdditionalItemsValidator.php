<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class AdditionalItemsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AdditionalItems) {
            throw new UnexpectedTypeException($constraint, AdditionalItems::class);
        }

        if (!isList($value) || ($constraint->additionalItemsSchema === true)) {
            return;
        }

        if (count($value) > $constraint->tupleLength) {
            $additionalItems = array_slice($value, $constraint->tupleLength, null, true);
            $context = $this->context;

            if ($constraint->additionalItemsSchema === false) {
                foreach ($additionalItems as $index => $item) {
                    $context
                        ->buildViolation(AdditionalItems::UNEXPECTED_ITEM_MESSAGE)
                        ->setParameter('{{ index }}', (string) $index)
                        ->setCode(AdditionalItems::UNEXPECTED_ITEM_ERROR)
                        ->atPath("[{$index}]")
                        ->setInvalidValue($item)
                        ->addViolation();
                }

                return;
            }

            $context
                ->getValidator()
                ->inContext($context)
                ->validate(
                    $additionalItems,
                    new All(
                        new Schema(
                            $constraint->additionalItemsSchema,
                            $constraint->getSchemaPath(),
                            $constraint->configuration
                        )
                    )
                );
        }
    }
}
