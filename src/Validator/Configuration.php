<?php declare(strict_types=1);

namespace OAS\Validator;

use OAS\Validator\Constraints\Format\DateTime;
use OAS\Validator\Constraints\Format\Duration;
use OAS\Validator\Constraints\Format\Email;
use OAS\Validator\Constraints\Format\Hostname;
use OAS\Validator\Constraints\Format\Ip;
use OAS\Validator\Constraints\Format\Uri;
use OAS\Validator\Constraints\Format\Uuid;
use Symfony\Component\Validator\Constraint as BaseConstraint;


class Configuration
{
    const ON_UNSUPPORTED_FORMAT_DO_NOTHING = 0;

    const ON_UNSUPPORTED_FORMAT_TRIGGER_WARNING = 1;

    const ON_UNSUPPORTED_FORMAT_THROW_EXCEPTION = 2;

    const ON_UNSUPPORTED_FORMAT_FAIL_VALIDATION = 3;

    public bool $stopOnFirstError;

    public int $unsupportedFormatBehaviour;

    public bool $yieldFormatSpecificError;

    public int $multipleOfScale;

    /**
     * a map
     *  [type <"string"|"number">][format <string>] => constraint <Constraint>
     *
     * @var array
     */
    private array $formatValidators;

    public function __construct(
        bool $stopOnFirstError = false,
        int $unsupportedFormatBehaviour = self::ON_UNSUPPORTED_FORMAT_DO_NOTHING,
        bool $yieldFormatSpecificError = false,
        int $multipleOfScale = 10
    ) {
        $this->stopOnFirstError = $stopOnFirstError;
        $this->unsupportedFormatBehaviour = $unsupportedFormatBehaviour;
        $this->yieldFormatSpecificError = $yieldFormatSpecificError;
        $this->multipleOfScale = $multipleOfScale;

        $this->initializeFormatConstraints();
    }

    private function initializeFormatConstraints(): void
    {
        $this->addFormatConstraint('string', 'email', fn () => new Email());
        $this->addFormatConstraint('string', 'uuid', fn () => new Uuid());
        $this->addFormatConstraint('string', 'uri', fn () => new Uri());
        $this->addFormatConstraint('string', 'ipv4', fn () => new Ip(['version' => '4']));
        $this->addFormatConstraint('string', 'ipv6', fn () => new Ip(['version' => '6']));
        $this->addFormatConstraint('string', 'hostname', fn () => new Hostname());
        $this->addFormatConstraint('string', 'date-time', fn () => new DateTime(['format' => \DateTimeInterface::RFC3339]));
        $this->addFormatConstraint('string', 'duration', fn () => new Duration());
    }

    /**
     * @param string $type
     * @param string $format
     * @param BaseConstraint|callable $constraint
     */
    public function addFormatConstraint(string $type, string $format, $constraint): void
    {
        if (!is_callable($constraint) && !$constraint instanceof BaseConstraint) {
            throw new \TypeError();
        }

        $this->formatValidators[$type][$format] = $constraint;
    }

    public function getFormatConstraint(string $type, string $format): ?BaseConstraint
    {
        $constraint = $this->formatValidators[$type][$format] ?? null;

        return is_callable($constraint) ? call_user_func($constraint) : $constraint;
    }
}
