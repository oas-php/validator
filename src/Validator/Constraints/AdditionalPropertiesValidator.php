<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Symfony\ExecutionContext;
use stdClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\{extract, isObject};

class AdditionalPropertiesValidator extends ConstraintValidator
{
    public function validate(mixed $instance, Constraint $constraint): void
    {
        if (!$constraint instanceof AdditionalProperties) {
            throw new UnexpectedTypeException($constraint, AdditionalProperties::class);
        }

        if (!isObject($instance)) {
            return;
        }

        $enclosingSchema = $constraint->enclosingSchemaConstraint->schema;
        $knownPropertyNames = array_keys($enclosingSchema->getProperties() ?? []);
        $knownPropertyPatterns = array_keys($enclosingSchema->getPatternProperties() ?? []);


        if ($instance instanceof stdClass) {
            $instance = (array) $instance;
        }

        $additionalPropertyNames = array_filter(
            array_keys($instance),
            function (string $propertyName) use ($knownPropertyNames, $knownPropertyPatterns) {
                if (in_array($propertyName, $knownPropertyNames)) {
                    return false;
                }

                foreach ($knownPropertyPatterns as $pattern) {
                    if (1 === preg_match("/$pattern/", $propertyName)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $context = $this->context;
        assert($context instanceof ExecutionContext);

        if (!empty($additionalPropertyNames)) {
            if (is_bool($constraint->schema) && !$constraint->schema) {
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

            foreach ($additionalPropertyNames as $additionalPropertyName) {
                $context
                    ->getValidator()
                    // TODO: should it be validated in separate context? in other words
                    // should the errors be nested?
                    //
                    // the "/foo" parameter is additional and does not conforms to schema
                    //      * /color type is invalid
                    //
                    // or rather just
                    //  /foo/color type is invalid
                    ->inContext($context)
                    ->atPath("[$additionalPropertyName]")
                    ->validate(
                        $instance[$additionalPropertyName],
                        new Schema(
                            $constraint->schema,
                            $constraint->getSchemaPath(),
                            $constraint->configuration,
                            $constraint->enclosingSchemaConstraint
                        )
                    );
            }
        }
    }
}
