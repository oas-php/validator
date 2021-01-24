<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class DependentRequiredValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint): void
    {
        if (!$constraint instanceof DependentRequired) {
            throw new UnexpectedTypeException($constraint, DependentRequired::class);
        }

        if (!isObject($instance)) {
            return;
        }

        $properties = array_keys((array) $instance);

        foreach ($constraint->dependencies as $property => $propertyDependencies) {
            if (in_array($property, $properties)) {
                $missingProperties = array_diff($propertyDependencies, $properties);

                if (!empty($missingProperties)) {
                    $this->context
                        ->buildViolation(DependentRequired::DEPENDENT_PROPERTIES_MISSING_MESSAGE)
                        ->setCode(DependentRequired::DEPENDENT_PROPERTIES_MISSING_ERROR)
                        ->setParameter('{{ property }}', $property)
                        ->setParameter('{{ missing_properties }}', join(', ', $missingProperties))
                        ->addViolation();
                }
            }
        }
    }
}
