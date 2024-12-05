<?php declare(strict_types=1);

namespace OAS\Validator\Symfony;

use JsonException;
use OAS;
use OAS\Schema;
use OAS\Validator\Configuration;
use OAS\Validator\Symfony\Result;
use OAS\Validator\SchemaConformanceFailure;
use OAS\Validator\Constraints;
use stdClass;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidator;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class Validator implements OAS\Validator
{
    private SymfonyValidator $validator;
    private Configuration $configuration;

    public function __construct(
        ?Configuration $configuration = null,
        ?TranslatorInterface $translator = null,
        ?string $locale = null
    ) {
        $this->validator = $this->createSymfonyValidator($translator, $locale);
        $this->configuration = $configuration ?? new Configuration();
    }

    public function validate(
        stdClass|array|string|int|float|bool|null $instance,
        Schema|bool $schema,
        Configuration $configuration = null
    ): Result {
        $constraint = new Constraints\Schema($schema, '#', $configuration ?? $this->configuration);
        $violations = $this->validator->validate($instance, $constraint);

        if ($violations->count() > 0) {
            throw new SchemaConformanceFailure(
                $instance,
                $constraint->schema,
                $violations
            );
        }

        return new Result($violations);
    }

    /**
     * @throws SchemaConformanceFailure
     */
    public function assertValid(
        stdClass|array|string|int|float|bool|null $instance,
        Schema|bool $schema,
        Configuration $configuration = null
    ): void
    {
        $constraint = new Constraints\Schema($schema, '#', $configuration ?? $this->configuration);
        $violations = $this->validator->validate($instance, $constraint);

        if ($violations->count() > 0) {
            throw new SchemaConformanceFailure(
                $instance,
                $constraint->schema,
                $violations
            );
        }
    }

    /**
     * @throws SchemaConformanceFailure
     * @throws JsonException
     */
    public function validateJSON(string $value, Schema|bool $schema, Configuration $configuration = null): void
    {
        $decoded = json_decode($value, false, 512, JSON_THROW_ON_ERROR);

        $this->validate($decoded, $schema, $configuration ?? $this->configuration);
    }

    private function createSymfonyValidator(?TranslatorInterface $translator, ?string $locale): SymfonyValidator
    {
        $translator = $translator ??  new class() implements TranslatorInterface, LocaleAwareInterface {
            use TranslatorTrait;
        };

        if (!is_null($locale)) {
            $translator->setLocale($locale);
        }

        $executionContextFactory = new ExecutionContextFactory($translator);
        $metadataFactory = new LazyLoadingMetadataFactory(new LoaderChain([]));

        return new RecursiveValidator($executionContextFactory, $metadataFactory, new ConstraintValidatorFactory(), []);
    }
}
