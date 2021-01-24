<?php declare(strict_types=1);

namespace OAS;

use OAS\Validator\Configuration;
use OAS\Validator\SchemaConformanceFailure;
use OAS\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use OAS\Validator\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class Validator
{
    private ValidatorInterface $validator;
    private Configuration $configuration;

    public function __construct(
        Configuration $configuration = null,
        TranslatorInterface $translator = null,
        string $locale = null
    ) {
        $this->validator = $this->createSymfonyValidator($translator, $locale);
        $this->configuration = $configuration ?? new Configuration();
    }

    /**
     * @param array|string|int|float|bool $instance graph of primitive values / primitive value (e.g the return value of json_decode($json);
     * @param Schema $schema
     * @param Configuration|null $configuration
     * @throws SchemaConformanceFailure
     */
    public function validate($instance, Schema $schema, Configuration $configuration = null): void
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
     * @param string $value json-encoded string
     * @param Schema $schema
     * @param Configuration|null $configuration
     * @throws SchemaConformanceFailure
     * @throws \JsonException
     */
    public function validateJSON(string $value, Schema $schema, Configuration $configuration = null): void
    {
        $decoded = json_decode($value, false, 512, JSON_THROW_ON_ERROR);

        $this->validate($decoded, $schema, $configuration ?? $this->configuration);
    }

    private function createSymfonyValidator(?TranslatorInterface $translator, ?string $locale): ValidatorInterface
    {
        $translator = $translator ??  new class() implements TranslatorInterface, LocaleAwareInterface {
            use TranslatorTrait;
        };

        if (!is_null($locale)) {
            $translator->setLocale($locale);
        }

        $executionContextFactory = new ExecutionContextFactory($translator);
        $metadataFactory = new LazyLoadingMetadataFactory(
            new LoaderChain([])
        );

        return new RecursiveValidator($executionContextFactory, $metadataFactory, new ConstraintValidatorFactory(), []);
    }
}
