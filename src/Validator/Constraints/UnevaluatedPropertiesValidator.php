<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\ExecutionContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class UnevaluatedPropertiesValidator extends ConstraintValidator
{
    public function __construct()
    {
    }

    public function validate(mixed $instance, Constraint $constraint): void
    {
        if (!$constraint instanceof UnevaluatedProperties) {
            throw new UnexpectedTypeException($constraint, UnevaluatedProperties::class);
        }

        if (!isObject($instance)) {
            return;
        }

        if ($instance instanceof \stdClass) {
            $instance = (array) $instance;
        }

        assert($this->context instanceof ExecutionContext);

        foreach ($instance as $propertyName => $value) {
            $successfullyEvaluatedPaths = $constraint->successfullyEvaluatedPaths->getArrayCopy();
            $isPathSuccessfullyEvaluated = in_array($propertyName, $successfullyEvaluatedPaths);

            if (!$isPathSuccessfullyEvaluated) {
                if ($constraint->schema === false) {
                    $this->context
                        ->buildViolation(UnevaluatedProperties::UNEVALUATED_PROPERTY_MESSAGE)
                        ->setParameter('{{ property }}', $propertyName)
                        ->setCode(UnevaluatedProperties::UNEVALUATED_PROPERTY_ERROR)
                        ->atPath("[$propertyName]")
                        ->setInvalidValue($value)
                        ->addViolation();

                    return;
                }

                $this->context
                    ->getValidator()
                    ->inContext($this->context)
                    ->atPath("[$propertyName]")
                    ->validate(
                        $value,
                        new Schema(
                            $constraint->schema,
                            $constraint->getSchemaPath(),
                            $constraint->configuration,
                            $constraint->parentSchemaConstraint
                        )
                    );
            }
        }
    }
}
