<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class PatternPropertiesValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof PatternProperties) {
            throw new UnexpectedTypeException($constraint, PatternProperties::class);
        }

        if (!isObject($instance)) {
            return;
        }

        $instance = (array) $instance;

        foreach ($constraint->schemas as $pattern => $schema) {
            $schema = new Schema(
                $schema, "{$constraint->getSchemaPath()}/{$pattern}", $constraint->configuration
            );

            foreach ($instance as $propertyName => $value) {
                if (1 === preg_match("/{$pattern}/", $propertyName)) {
                    $this->context
                        ->getValidator()
                        ->inContext($this->context)
                        ->atPath("[{$propertyName}]")
                        ->validate($value, $schema);
                }
            }
        }
    }
}
