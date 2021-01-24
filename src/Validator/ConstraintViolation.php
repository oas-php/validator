<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\Constraint as BaseConstraint;
use Symfony\Component\Validator\ConstraintViolation as BaseConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolation extends BaseConstraintViolation
{
    /** @var array ConstraintViolationListInterface[] */
    private array $nestedViolations;

    private BaseConstraint $constraint;

    public function __construct(
        $message,
        ?string $messageTemplate,
        array $parameters,
        $root,
        ?string $propertyPath,
        $invalidValue,
        BaseConstraint $constraint,
        int $plural = null,
        string $code = null,
        array $nestedViolations = [],
        $cause = null

    ) {
        parent::__construct(
            $message,
            $messageTemplate,
            $parameters,
            $root,
            $propertyPath,
            $invalidValue,
            $plural,
            $code,
            $constraint,
            $cause
        );
        $this->constraint = $constraint;
        $this->nestedViolations = $nestedViolations;
    }

    /**
     * @return ConstraintViolationListInterface[]
     */
    public function getNestedViolations(): array
    {
        return $this->nestedViolations;
    }

    public function getInstancePath(): string
    {
        return $this->formatInstancePath(
            empty($propertyPath = $this->getPropertyPath())
                ? '#/' : $propertyPath
        );
    }

    public function getSchemaPath(): string
    {
        if (!$this->constraint instanceof SchemaPathAwareConstraint) {
            throw new \LogicException(
                sprintf(
                    '%s should be instantiated with constraint violation which implements %s',
                    __CLASS__,
                    SchemaPathAwareConstraint::class
                )
            );
        }

        return $this->constraint->getSchemaPath();
    }

    private function formatInstancePath(string $instancePath): string
    {
        return str_replace(['][', '[', ']'], ['/', '#/', ''], $instancePath);
    }
}
