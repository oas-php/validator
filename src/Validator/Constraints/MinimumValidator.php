<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator;
use function OAS\Validator\isNumber;

class MinimumValidator extends GreaterThanOrEqualValidator
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
        return Minimum::TOO_LOW_ERROR;
    }
}
