<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS\Validator\Configuration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FormatValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Format) {
            throw new UnexpectedTypeException($constraint, Format::class);
        }

        $formatConstraint = $constraint->configuration->getFormatConstraint(gettype($value), $constraint->format);

        if (!is_null($formatConstraint)) {
            $violations = $this->context
                ->getValidator()
                ->validate($value, [$formatConstraint]);

            if (count($violations) > 0) {
                if ($constraint->configuration->yieldFormatSpecificError) {
                    $violation = $violations->get(0);
                    $message = $violation->getMessage();
                    $code = $violation->getCode();
                } else {
                    $message = Format::FORMAT_MISMATCH_MESSAGE;
                    $code = Format::FORMAT_MISMATCH_ERROR;
                }

                $this->context
                    ->buildViolation($message)
                    ->setCode($code)
                    ->setParameter('{{ value }}', $value)
                    ->setParameter('{{ format }}', $constraint->format)
                    ->addViolation();
            }
        } else {
            switch ($constraint->configuration->unsupportedFormatBehaviour) {
                case Configuration::ON_UNSUPPORTED_FORMAT_TRIGGER_WARNING :
                    trigger_error(
                        $this->getUnsupportedFormatMessage($value, $constraint->format),
                        E_USER_WARNING
                    );

                    break;

                case Configuration::ON_UNSUPPORTED_FORMAT_FAIL_VALIDATION :
                    $this->context
                        ->buildViolation(Format::UNSUPPORTED_FORMAT_MESSAGE)
                        ->setCode(Format::UNSUPPORTED_FORMAT_ERROR)
                        ->setParameter('{{ type }}', $this->formatTypeOf($value))
                        ->setParameter('{{ format }}', $constraint->format)
                        ->addViolation();

                    break;

                case Configuration::ON_UNSUPPORTED_FORMAT_THROW_EXCEPTION :
                    throw new \RuntimeException(
                        $this->getUnsupportedFormatMessage($value, $constraint->format)
                    );
            }
        }
    }

    private function getUnsupportedFormatMessage($instance, string $format): string
    {
        return sprintf('Unsupported "%s" format for "%s" type', $format, $this->formatTypeOf($instance));
    }
}
