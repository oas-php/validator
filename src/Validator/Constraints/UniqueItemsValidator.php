<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\{isList, equal, normalize};

class UniqueItemsValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueItems) {
            throw new UnexpectedTypeException($constraint, UniqueItems::class);
        }

        if (!isList($instance)) {
            return;
        }

        $items = normalize($instance);

        while (!empty($items)) {
            $current = array_shift($items);

            foreach ($items as $item) {
                if (equal($current, $item)) {
                    $this->context
                        ->buildViolation(UniqueItems::NOT_UNIQUE_MESSAGE)
                        ->setCode(UniqueItems::NOT_UNIQUE_ERROR)
                        ->addViolation();
                }
            }
        }
    }
}
