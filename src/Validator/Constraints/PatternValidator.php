<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\RegexValidator;

class PatternValidator extends RegexValidator
{
    public function validate($instance, Constraint $pattern): void
    {
        if (!is_string($instance)) {
            return;
        }

        parent::validate($instance, $pattern);
    }
}
