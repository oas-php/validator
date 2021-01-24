<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\CountValidator as BaseCountValidator;
use function OAS\Validator\isList;

class CountValidator extends BaseCountValidator
{
    public function validate($value, Constraint $constraint)
    {
        switch (get_class($constraint)) {
            case MaxProperties::class:
            case MinProperties::class:
                // Both cases are supported: JSON objects represented
                //  - as \stdClass instances (e.g obtained with \json_decode($json, false))
                //  - as associative arrays  (e.g obtained with \json_decode($json, true) - not recommended!)
                //
                // in latter case, if JSON object is empty we got an empty PHP array:
                //  \json_decode("{}", true) == []
                // which we can not distinguish from empty JSON array:
                //  \json_decode("[]", true) == []
                //
                // and according to JSONSchema spec, non-object values must be ignored.
                if (isList($value)) {
                    // TODO: emit warning!
                    // trigger_error('', E_USER_WARNING);
                    return;
                }

                if ($value instanceof \stdClass) {
                    $value = (array) $value;
                }

                if (!is_array($value)) {
                    return;
                }

                break;

            case MinItems::class:
            case MaxItems::class:
                if (!is_array($value)) {
                    return;
                }

                break;
        }

        parent::validate($value, $constraint);
    }
}
