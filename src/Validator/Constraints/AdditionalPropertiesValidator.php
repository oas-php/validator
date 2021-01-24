<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\{extract, isObject};

class AdditionalPropertiesValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint): void
    {
        if (!$constraint instanceof AdditionalProperties) {
            throw new UnexpectedTypeException($constraint, AdditionalProperties::class);
        }

        if (!isObject($instance) || $constraint->schema->isAlwaysValid()) {
            return;
        }

        if ($instance instanceof \stdClass) {
            $instance = (array) $instance;
        }

        $additionalPropertyNames = array_filter(
            array_keys($instance),
            function (string $propertyName) use ($constraint) {
                if (in_array($propertyName, $constraint->knownPropertyNames)) {
                    return false;
                }

                foreach ($constraint->knownPropertyPatterns as $pattern) {
                    if (1 === preg_match("/$pattern/", $propertyName)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $context = $this->context;

        if (!empty($additionalPropertyNames)) {
            if ($constraint->schema->isAlwaysInvalid()) {
                foreach ($additionalPropertyNames as $additionalPropertyName) {
                    $context
                        ->buildViolation(AdditionalProperties::UNEXPECTED_PROPERTY_MESSAGE)
                        ->setParameter('{{ property }}', $additionalPropertyName)
                        ->setCode(AdditionalProperties::UNEXPECTED_PROPERTY_ERROR)
                        ->atPath("[{$additionalPropertyName}]")
                        ->setInvalidValue($instance[$additionalPropertyName])
                        ->addViolation();
                }

                return;
            }

            $context
                ->getValidator()
                ->inContext($context)
                ->validate(
                    extract($additionalPropertyNames, $instance),
                    new All(
                        new Schema(
                            $constraint->schema,
                            $constraint->getSchemaPath(),
                            $constraint->configuration
                        )
                    )
                );
        }
    }
}
