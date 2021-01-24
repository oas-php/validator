<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanValidator;
use function OAS\Validator\isNumber;

class ExclusiveMinimumValidator extends GreaterThanValidator
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
        return ExclusiveMinimum::TOO_LOW_ERROR;
    }
}
