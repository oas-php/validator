<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class RequiredValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint): void
    {
        if (!$constraint instanceof Required) {
            throw new UnexpectedTypeException($constraint, Required::class);
        }

        if (!isObject($instance)) {
            return;
        }

        $missingProperties = array_diff($constraint->properties, array_keys((array) $instance));

        if (!empty($missingProperties)) {
                $this->context
                    ->buildViolation(Required::REQUIRED_PROPERTIES_MISSING_MESSAGE)
                    ->setCode(Required::REQUIRED_PROPERTIES_MISSING_ERROR)
                    ->setPlural(count($constraint->properties))
                    ->setParameter('{{ required_properties }}', join(', ', $missingProperties))
                    ->addViolation();
        }
    }
}
