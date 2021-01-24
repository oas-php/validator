<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Util\PropertyPath;
use Symfony\Component\Validator\Constraint as BaseConstraint;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintViolationBuilder implements ConstraintViolationBuilderInterface
{
    private ConstraintViolationList $violations;

    private BaseConstraint $constraint;

    private string $message;

    private array $parameters;

    /** @var mixed */
    private $root;

    /** @var mixed */
    private $invalidValue;

    private string $propertyPath;

    private TranslatorInterface $translator;

    private $translationDomain;

    private ?int $plural;

    /** @var mixed */
    private $code;

    /** @var mixed */
    private $cause;

    private array $nestedConstraintViolations;

    public function __construct(
        ConstraintViolationList $violations,
        BaseConstraint $constraint,
        string $message,
        array $parameters,
        $root,
        $propertyPath,
        $invalidValue,
        TranslatorInterface $translator,
        $translationDomain = null
    ) {
        $this->violations = $violations;
        $this->message = $message;
        $this->parameters = $parameters;
        $this->root = $root;
        $this->propertyPath = $propertyPath;
        $this->invalidValue = $invalidValue;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->constraint = $constraint;
        $this->nestedConstraintViolations = [];
        $this->plural = null;
    }

    public function atPath(string $path): static
    {
        $this->propertyPath = PropertyPath::append($this->propertyPath, $path);

        return $this;
    }

    public function setParameter(string $key, string $value): static
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setTranslationDomain(string $translationDomain): static
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function setInvalidValue($invalidValue): static
    {
        $this->invalidValue = $invalidValue;

        return $this;
    }

    public function setPlural(int $number): static
    {
        $this->plural = $number;

        return $this;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function setCause($cause): static
    {
        $this->cause = $cause;

        return $this;
    }

    public function setNestedConstraintViolations(array $nestedConstraintViolations): self
    {
        $this->nestedConstraintViolations = $nestedConstraintViolations;

        return $this;
    }

    public function addViolation()
    {
        if (null === $this->plural) {
            $translatedMessage = $this->translator->trans(
                $this->message,
                $this->parameters,
                $this->translationDomain
            );
        } else {
            $translatedMessage = $this->translator->trans(
                $this->message,
                ['%count%' => $this->plural] + $this->parameters,
                $this->translationDomain
            );
        }

        $this->violations->add(
            new ConstraintViolation(
                $translatedMessage,
                $this->message,
                $this->parameters,
                $this->root,
                $this->propertyPath,
                $this->invalidValue,
                $this->constraint,
                $this->plural,
                $this->code,
                $this->nestedConstraintViolations,
                $this->cause
            )
        );
    }
}
