<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Sequentially;

class Schema extends Constraint
{
    const ALWAYS_INVALID_ERROR = '37c61162-b092-472c-84df-6973810d559f';

    const ALWAYS_INVALID_MESSAGE = 'Schema always fails validation';

    public OAS\Schema $schema;

    public function __construct(OAS\Schema $schema, string $path, Configuration $configuration = null)
    {
        $this->schema = $schema;
        parent::__construct($path, $configuration ?? new Configuration());
    }

    public static function createFromSchema(OAS\Schema $schema, string $path = '#', Configuration $configuration = null): self
    {
        return new self($schema, $path, $configuration);
    }

    /**
     * @return Constraint[]
     */
    public function buildConstraint(): array
    {
        return $this->build($this->schema, $this->getSchemaPath() ?? '#');
    }

    /**
     * @param OAS\Schema $schema
     * @param string $path
     * @return Constraint[]
     */
    private function build(OAS\Schema $schema, string $path): array
    {
        $constraints = [];

        if ($schema->hasType()) {
            $constraints[] = new Type(
                $schema->getType(), $path
            );
        }

        if ($schema->hasMinLength()) {
            $constraints[] = new MinLength(
                $schema->getMinLength(), $path
            );
        }

        if ($schema->hasMaxLength()) {
            $constraints[] = new MaxLength(
                $schema->getMaxLength(), $path
            );
        }

        if ($schema->hasPattern()) {
            $constraints[] = new Pattern(
                "/{$schema->getPattern()}/", $path
            );
        }

        if ($schema->hasMinimum()) {
            $constraints[] = new Minimum(
                $schema->getMinimum(), $path
            );
        }

        if ($schema->hasExclusiveMinimum()) {
            $constraints[] = new ExclusiveMinimum(
                $schema->getExclusiveMinimum(), $path
            );
        }

        if ($schema->hasMaximum()) {
            $constraints[] = new Maximum(
                $schema->getMaximum(), $path
            );
        }

        if ($schema->hasExclusiveMaximum()) {
            $constraints[] = new ExclusiveMaximum(
                $schema->getExclusiveMaximum(), $path
            );
        }

        if ($schema->hasMultipleOf()) {
            $constraints[] = new MultipleOf(
                $schema->getMultipleOf(), $path, $this->configuration->multipleOfScale
            );
        }

        if ($schema->hasProperties()) {
            $constraints[] = new Properties(
                $schema->getProperties(),
                $path,
                $this->configuration
            );
        }

        if ($schema->hasPatternProperties()) {
            $constraints[] = new PatternProperties(
                $schema->getPatternProperties(),
                $path,
                $this->configuration
            );
        }

        if ($schema->hasPropertyNames()) {
            $constraints[] = new PropertyNames(
                $schema->getPropertyNames(),
                $path,
                $this->configuration
            );
        }

        if ($schema->hasRequired()) {
            $constraints[] = new Required(
                $schema->getRequired(), $path
            );
        }

        if ($schema->hasDependentRequired()) {
            $constraints[] = new DependentRequired(
                $schema->getDependentRequired(), $path
            );
        }

        if ($schema->hasAdditionalProperties()) {
            $knownPropertyNames = array_keys($schema->getProperties() ?? []);
            $knownPropertyPatterns = array_keys($schema->getPatternProperties() ?? []);

            $constraints[] = new AdditionalProperties(
                $schema->getAdditionalProperties(),
                $knownPropertyNames,
                $knownPropertyPatterns,
                $path,
                $this->configuration
            );
        }

        if ($schema->hasMinProperties()) {
            $constraints[] = new MinProperties(
                $schema->getMinProperties(), $path
            );
        }

        if ($schema->hasMaxProperties()) {
            $constraints[] = new MaxProperties(
                $schema->getMaxProperties(), $path
            );
        }

        if ($schema->hasMinItems()) {
            $constraints[] = new MinItems(
                $schema->getMinItems(), $path
            );
        }

        if ($schema->hasMaxItems()) {
            $constraints[] = new MaxItems(
                $schema->getMaxItems(), $path
            );
        }

        if ($schema->hasUniqueItems() && $schema->getUniqueItems()) {
            $constraints[] = new UniqueItems($path);
        }

        if ($schema->hasItems()) {
            $items = $schema->getItems();

            $constraints[] = new Items(
                $items, $path, $this->configuration
            );

            if (is_array($items) && $schema->hasAdditionalItems()) {
                $tupleLength = count($items);

                $constraints[] = new AdditionalItems(
                    $schema->getAdditionalItems(),
                    $tupleLength,
                    $path,
                    $this->configuration
                );
            }
        }

        if ($schema->hasContains()) {
            $hasMinContains = $schema->hasMinContains();

            if (!$hasMinContains) {
                $constraints[] = new Contains(
                    $schema->getContains(), $path, $this->configuration
                );
            }

            if ($hasMinContains) {
                $constraints[] = new MinContains(
                    $schema->getContains(),
                    $schema->getMinContains(),
                    $path,
                    $this->configuration
                );
            }

            if ($schema->hasMaxContains()) {
                $constraints[] = new MaxContains(
                    $schema->getContains(),
                    $schema->getMaxContains(),
                    $path,
                    $this->configuration
                );
            }
        }

        if ($schema->hasFormat()) {
            $constraints[] = new Format(
                $schema->getFormat(), $path, $this->configuration
            );
        }

        if ($schema->hasEnum()) {
            $constraints[] = new Enum(
                $schema->getEnum(), $path
            );
        }

        if ($schema->hasConst()) {
            $constraints[] = new Constant(
                $schema->getConst(), $path
            );
        }

        if ($schema->hasIf()) {
            $constraints[] = new IfThenElse(
                $schema->getIf(),
                $schema->getThen(),
                $schema->getElse(),
                $path,
                $this->configuration
            );
        }

        if ($schema->hasDependentSchemas()) {
            $constraints[] = new DependentSchemas(
                $schema->getDependentSchemas(), $path, $this->configuration
            );
        }

        if ($schema->hasNot()) {
            $constraints[] = new Not(
                $schema->getNot(), $path, $this->configuration
            );
        }

        if ($schema->hasAnyOf()) {
            $constraints[] = new AnyOf(
                $schema->getAnyOf(), $path, $this->configuration
            );
        }

        if ($schema->hasOneOf()) {
            $constraints[] = new OneOf(
                $schema->getOneOf(), $path, $this->configuration
            );
        }

        if ($schema->hasAllOf()) {
            $constraints[] = new AllOf(
                $schema->getAllOf(), $path, $this->configuration
            );
        }

        if ($this->configuration->stopOnFirstError) {
            return $constraints;
        }

        return empty($constraints) ? $constraints : [new Sequentially($constraints)];
    }
}
