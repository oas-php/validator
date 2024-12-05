<?php declare(strict_types=1);

namespace OAS\Validator\Symfony;

use ArrayObject;
use OAS\Schema;
use OAS\Validator\Symfony\ConstraintViolationBuilder;
use SplStack;
use Stringable;
use Symfony\Component\Validator\Context\ExecutionContext as BaseExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExecutionContext extends BaseExecutionContext
{
    private TranslatorInterface $translator;
    private ?string $translationDomain;

    public function __construct(
        private SplStack $dynamicScope,
        ValidatorInterface $validator,
        $root,
        TranslatorInterface $translator,
        string $translationDomain = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        parent::__construct($validator, $root, $translator, $translationDomain);
    }

    public function buildViolation(string|Stringable $message, array $parameters = []): ConstraintViolationBuilderInterface
    {
        return new ConstraintViolationBuilder(
            $this->getViolations(),
            $this->getConstraint(),
            $message,
            $parameters,
            $this->getRoot(),
            $this->getPropertyPath(),
            $this->getValue(),
            $this->translator,
            $this->translationDomain
        );
    }

    public function enterSchemaResource(Schema $schema): void
    {
        $this->dynamicScope->push($schema);
    }

    public function leaveSchemaResource(): void
    {
        $this->dynamicScope->pop();
    }

    public function getCurrentSchemaResource(): ?Schema
    {
        return $this->dynamicScope->isEmpty() ? null : $this->dynamicScope->top();
    }

    public function getDynamicScope(): array
    {
        $dynamicScope = [];

        /** @var Schema $schemaResource */
        foreach (clone $this->dynamicScope as $schemaResource) {
            array_unshift($dynamicScope, $schemaResource->getResolvedId() ?? $schemaResource->getId());
        }

        return $dynamicScope;
    }

    public function getCurrentScope(): string
    {
        $dynamicScope = $this->getDynamicScope();

        return array_shift($dynamicScope);
    }
}
