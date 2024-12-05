<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Symfony\ExecutionContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\CollectionValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\isObject;

class PropertiesValidator extends CollectionValidator
{
    public function validate(mixed $instance, Constraint $constraint): void
    {
        if (!$constraint instanceof Properties) {
            throw new UnexpectedTypeException($constraint, Properties::class);
        }

        if (!isObject($instance)) {
            return;
        }

        parent::validate((array) $instance, $constraint);
    }
}
