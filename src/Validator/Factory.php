<?php declare(strict_types=1);

namespace OAS\Validator;

use OAS\Validator\Symfony\Validator as SymfonyValidator;
use OAS\Validator;

// TODO translation adapter please!
use Symfony\Contracts\Translation\TranslatorInterface;

class Factory
{
    public static function create(
        ?Configuration $configuration = null,
        ?TranslatorInterface $translator = null,
        string $locale = null
    ): Validator
    {
        return new SymfonyValidator($configuration, $translator, $locale);
    }
}