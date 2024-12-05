<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use stdClass;
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
            Schema\Type::NULL->value    => 'is_null',
            Schema\Type::BOOLEAN->value => 'is_bool',
            Schema\Type::STRING->value  => 'is_string',
            Schema\Type::INTEGER->value => fn ($value) => is_integer($value) || isZeroDecimalFloat($value),
            Schema\Type::NUMBER->value  => fn ($value) => isNumber($value),
            Schema\Type::ARRAY->value   => fn ($value) => isList($value),
            Schema\Type::OBJECT->value  => fn ($value) => $value instanceof stdClass || isMap($value)
        ];
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Type) {
            throw new UnexpectedTypeException($constraint, Type::class);
        }

        $allowedTypes = is_array($constraint->type) ? $constraint->type : [$constraint->type];

        /** @var Schema\Type $type */
        foreach ($allowedTypes as $allowedType) {
            if (call_user_func($this->validationFunctions[$allowedType->value], $value)) {
                return;
            }
        }

        $this->context->buildViolation(Type::INVALID_TYPE_MESSAGE)
            ->setCode(Type::INVALID_TYPE_ERROR)
            ->setParameter('{{ actual }}', $this->formatTypeOf($value))
            ->setParameter('{{ expected }}', $this->formatAllowedTypes($allowedTypes))
            ->addViolation();
    }

    protected function formatTypeOf($value): string
    {
        return isObject($value) ? 'object' : parent::formatTypeOf($value);
    }

    /**
     * @param array<int, Schema\Type> $allowedTypes
     */
    private function formatAllowedTypes(array $allowedTypes): string
    {
        return implode('|', array_map(fn (Schema\Type $type) => $type->value, $allowedTypes));
    }
}
