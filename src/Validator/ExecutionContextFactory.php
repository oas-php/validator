<?php declare(strict_types=1);

namespace OAS\Validator;

use Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExecutionContextFactory implements ExecutionContextFactoryInterface
{
    private TranslatorInterface $translator;
    private \ArrayObject $annotations;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->annotations = new \ArrayObject();
    }

    public function createContext(ValidatorInterface $validator, $root): ExecutionContext
    {
        return new ExecutionContext(
            $validator,
            $root,
            $this->annotations,
            $this->translator
        );
    }
}
