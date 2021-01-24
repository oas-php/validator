<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use OAS\Schema;
use function OAS\Validator\{isMap, isList, isNumber, isObject, isZeroDecimalFloat};

class TypeValidator extends ConstraintValidator
{
    private array $validationFunctions;

    public function __construct()
    {
        $this->validationFunctions = [
            'null'    => 'is_null',
            'boolean' => 'is_bool',
            'string'  => 'is_string',
            'integer' => fn ($value) => is_integer($value) || isZeroDecimalFloat($value),
            'number'  => fn ($value) => isNumber($value),
            'array'   => fn ($value) => isList($value),
            'object'  => fn ($value) => $value instanceof \stdClass || isMap($value)
        ];
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Type) {
            throw new UnexpectedTypeException($constraint, Type::class);
        }

        $types = (array) $constraint->type;

        foreach ($types as $type) {
            $type = strtolower($type);

            if (!array_key_exists($type, $this->validationFunctions)) {
                // this should never happen as OAS\Schema::getType ensures
                // only supported types are set
                throw new \LogicException(
                    sprintf(
                        'Type %s is not supported (supported types are: %s)',
                        $type,
                        join('|', Schema::TYPES)
                    )
                );
            }

            if (call_user_func($this->validationFunctions[$type], $value)) {
                return;
            }
        }

        $this->context->buildViolation(Type::INVALID_TYPE_MESSAGE)
            ->setCode(Type::INVALID_TYPE_ERROR)
            ->setParameter('{{ actual }}', $this->formatTypeOf($value))
            ->setParameter('{{ expected }}', implode('|', $types))
            ->addViolation();
    }

    protected function formatTypeOf($value): string
    {
        return isObject($value) ? 'object' : parent::formatTypeOf($value);
    }
}
