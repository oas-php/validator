<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class MinContainsValidator extends ConstraintValidator
{
    public function validate($items, Constraint $constraint)
    {
        if (!$constraint instanceof MinContains) {
            throw new UnexpectedTypeException($constraint, MinContains::class);
        }

        if (!isList($items)) {
            return;
        }

        $validCount = 0;
        $nestedViolations = [];
        $context = $this->context;
        $containsSchema = new Schema(
            $constraint->schema, '#', $constraint->configuration
        );

        if ($constraint->min > 0) {
            foreach ($items as $item) {
                $violations = $context
                    ->getValidator()
                    ->validate($item, $containsSchema);

                if ($violations->count() > 0) {
                    $nestedViolations[] = $violations;
                } else {
                    if (++$validCount >= $constraint->min) {
                        return;
                    }
                }
            }

            $constraintViolationBuilder = $context
                ->buildViolation(MinContains::TOO_FEW_MESSAGE)
                ->setPlural($constraint->min)
                ->setCode(MinContains::TOO_FEW_ERROR)
                ->setParameter('{{ min }}', (string) $constraint->min);

            if ($constraintViolationBuilder instanceof Validator\ConstraintViolationBuilder) {
                $constraintViolationBuilder->setNestedConstraintViolations($nestedViolations);
            }

            $constraintViolationBuilder->addViolation();
        }
    }
}
