<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class Items2019_09Validator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Items2019_09) {
            throw new UnexpectedTypeException($constraint, Items2019_09::class);
        }

        if (!isList($value) || empty($value)) {
            return;
        }

        $context = $this->context;
        $itemCount = count($value);

        if (is_array($constraint->schemas)) {
            foreach ($constraint->schemas as $index => $schema) {
                $violations = $context
                    ->getValidator()
                    ->inContext($context)
                    ->atPath('['.$index.']')
                    ->validate(
                        $value[$index],
                        new Schema(
                            $schema,
                            $constraint->getSchemaPath(),
                            $constraint->configuration
                        )
                    )
                    ->getViolations();

                if ($violations->count() > 0 || $itemCount == $index + 1) {
                    break;
                }
            }
        } else {
            $schema = new Schema(
                $constraint->schemas,
                $constraint->getSchemaPath(),
                $constraint->configuration
            );

            foreach ($value as $index => $item) {
                $context
                    ->getValidator()
                    ->inContext($context)
                    ->atPath('['.$index.']')
                    ->validate($item, $schema)
                    ->getViolations();
            }
        }
    }
}
