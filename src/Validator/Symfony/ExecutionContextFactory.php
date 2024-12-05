<?php declare(strict_types=1);

namespace OAS\Validator\Symfony;

use OAS\Validator\Symfony\ExecutionContext;
use SplStack;
use Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExecutionContextFactory implements ExecutionContextFactoryInterface
{
    private TranslatorInterface $translator;
    private SplStack $dynamicScope;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->dynamicScope = new SplStack();
    }

    public function createContext(ValidatorInterface $validator, $root): ExecutionContext
    {
        return new ExecutionContext(
            $this->dynamicScope,
            $validator,
            $root,
            $this->translator,
        );
    }
}
