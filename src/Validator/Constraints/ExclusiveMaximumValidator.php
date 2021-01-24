<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanValidator;
use function OAS\Validator\isNumber;

class ExclusiveMaximumValidator extends LessThanValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!isNumber($value)) {
            return;
        }

        parent::validate($value, $constraint);
    }

    protected function getErrorCode(): string
    {
        return ExclusiveMaximum::TOO_HIGH_ERROR;
    }
}
