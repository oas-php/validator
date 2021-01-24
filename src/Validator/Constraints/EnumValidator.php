<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\{equal, normalize};

class EnumValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, Enum::class);
        }

        if ($instance instanceof \stdClass) {
            $instance = (array) $instance;
        }

        $choices = normalize($constraint->choices);
        $instance = normalize($instance);

        foreach ($choices as $choice) {
            if (equal($instance, $choice)) {
                return;
            }
        }

        $this->context
            ->buildViolation(Enum::INVALID_CHOICE_MESSAGE)
            ->setCode(Enum::INVALID_CHOICE_ERROR)
            ->setParameter('{{ value }}', $this->formatValue($instance))
            ->setParameter('{{ choices }}', $this->formatValues($choices))
            ->addViolation();
    }
}
