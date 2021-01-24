<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\ExecutionContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isList;

class ItemsValidator extends ConstraintValidator
{
    public function validate($instance, Constraint $constraint)
    {
        if (!$constraint instanceof Items) {
            throw new UnexpectedTypeException($constraint, Items::class);
        }

        if (!isList($instance) || empty($instance)) {
            return;
        }

        $context = $this->context;
        $itemCount = count($instance);

        if (is_array($constraint->schemas)) {
            foreach ($constraint->schemas as $index => $schema) {
                $violations = $context
                    ->getValidator()
                    ->inContext($context)
                    ->atPath('['.$index.']')
                    ->validate(
                        $instance[$index],
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

                $evaluatedIndex = $index;
            }

            if ($context instanceof ExecutionContext && isset($evaluatedIndex)) {
                $context->annotate(get_class($constraint), $evaluatedIndex);
            }
        } else {
            $schema = new Schema(
                $constraint->schemas,
                $constraint->getSchemaPath(),
                $constraint->configuration
            );

            foreach ($instance as $index => $item) {
                $context
                    ->getValidator()
                    ->inContext($context)
                    ->atPath('['.$index.']')
                    ->validate($item, $schema)
                    ->getViolations();
            }

            if ($context instanceof ExecutionContext && isset($index)) {
                $context->annotate(get_class($constraint), true);
            }
        }
    }
}
