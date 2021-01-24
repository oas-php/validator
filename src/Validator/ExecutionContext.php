<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\Context\ExecutionContext as BaseExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExecutionContext extends BaseExecutionContext
{
    private TranslatorInterface $translator;
    private ?string $translationDomain;
    private \ArrayObject $annotations;

    public function annotate(string $keyword, $value): void
    {
        if (!$this->annotations->offsetExists($this->getPropertyPath())) {
            $this->annotations[$this->getPropertyPath()] = new \ArrayObject();
        }

        $instanceAnnotations = $this->annotations[$this->getPropertyPath()];

        if ($instanceAnnotations->offsetExists($keyword)) {
            $instanceAnnotations[$keyword] = is_bool($value)
                ? $value : max($instanceAnnotations[$keyword], $value);
        } else {
            $instanceAnnotations[$keyword] = $value;
        }
    }

    public function annotations()
    {
        //return $this->annotations[];
    }

    public function __construct(
        ValidatorInterface $validator,
        $root,
        \ArrayObject $annotations,
        TranslatorInterface $translator,
        string $translationDomain = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->annotations = $annotations;
        parent::__construct($validator, $root, $translator, $translationDomain);
    }

    public function buildViolation(string $message, array $parameters = []): ConstraintViolationBuilderInterface
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
}
