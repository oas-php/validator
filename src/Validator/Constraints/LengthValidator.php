<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LengthValidator as BaseLengthValidator;

class LengthValidator extends BaseLengthValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!is_string($value)) {
            return;
        }

        parent::validate($value, $constraint);
    }
}
