<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Symfony\ExecutionContext;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function OAS\Validator\normalizePropertyPath;

class SchemaValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Schema) {
            throw new UnexpectedTypeException($constraint, Schema::class);
        }

        assert($this->context instanceof ExecutionContext);

        $schema = $constraint->schema;
        // TODO should root schema without $id be considered a schema resource?
        $isSchemaResource = $schema instanceof OAS\Schema && $schema->hasId();

        if ($isSchemaResource) {
            $this->context->enterSchemaResource($schema);
        }

        $debugInstancePath = $this->getCurrentInstancePath();
        $debugKeywordPath = $constraint->getSchemaPath();
        $debugCurrentResource = $this->context->getCurrentSchemaResource()?->getId();
        $debugDynamicScope = $this->context->getDynamicScope();

        $isValid = $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, $this->build($constraint))
            ->getViolations()
            ->count() == 0;

        if ($isValid) {
            $this->markCurrentPathAsSuccessfullyEvaluated($constraint);
        }

        if ($isSchemaResource) {
            $this->context->leaveSchemaResource();
        }
    }

    /**
     * @return array<int, \OAS\Validator\Constraint>
     */
    private function build(Schema $schemaConstraint): array
    {
        $schema = $schemaConstraint->schema;

        if (is_bool($schema)) {
            return [new Boolean($schema, $schemaConstraint)];
        }

        $constraints = [];

        if ($schema->hasType()) {
            $constraints[] = new Type(
                $schema->getType(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMinLength()) {
            $constraints[] = new MinLength(
                $schema->getMinLength(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMaxLength()) {
            $constraints[] = new MaxLength(
                $schema->getMaxLength(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasPattern()) {
            $constraints[] = new Pattern(
                "/{$schema->getPattern()}/",
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMinimum()) {
            $constraints[] = new Minimum(
                $schema->getMinimum(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasExclusiveMinimum()) {
            $constraints[] = new ExclusiveMinimum(
                $schema->getExclusiveMinimum(),
                $schemaConstraint->schemaPath
            );
        }

        $maximum = $schema->getMaximum();

        if ($maximum !== null) {
            $constraints[] = new Maximum(
                $maximum,
                $schemaConstraint->schemaPath);
        }

        if ($schema->hasExclusiveMaximum()) {
            $constraints[] = new ExclusiveMaximum(
                $schema->getExclusiveMaximum(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMultipleOf()) {
            $constraints[] = new MultipleOf(
                $schema->getMultipleOf(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration->multipleOfScale
            );
        }

        if ($schema->hasProperties()) {
            $constraints[] = new Properties(
                $schema->getProperties(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint,
                $schemaConstraint->referencingSchemaConstraint
            );
        }

        if ($schema->hasPatternProperties()) {
            $constraints[] = new PatternProperties(
                $schema->getPatternProperties(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint
            );
        }

        if ($schema->hasPropertyNames()) {
            $constraints[] = new PropertyNames(
                $schema->getPropertyNames(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration
            );
        }

        if ($schema->hasRequired()) {
            $constraints[] = new Required(
                $schema->getRequired(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasDependentRequired()) {
            $constraints[] = new DependentRequired(
                $schema->getDependentRequired(),
                $schemaConstraint->schemaPath
            );
        }

        $additionalProperties = $schema->getAdditionalProperties();

        if ($additionalProperties !== null) {
            $constraints[] = new AdditionalProperties(
                $additionalProperties,
                $schemaConstraint
            );
        }

        if ($schema->hasMinProperties()) {
            $constraints[] = new MinProperties(
                $schema->getMinProperties(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMaxProperties()) {
            $constraints[] = new MaxProperties(
                $schema->getMaxProperties(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMinItems()) {
            $constraints[] = new MinItems(
                $schema->getMinItems(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasMaxItems()) {
            $constraints[] = new MaxItems(
                $schema->getMaxItems(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasUniqueItems() && $schema->getUniqueItems()) {
            $constraints[] = new UniqueItems($schemaConstraint->schemaPath);
        }

        $prefixItems = $schema->getPrefixItems();

        if ($prefixItems !== null) {
            $constraints[] = new PrefixItems(
                $prefixItems,
                $schemaConstraint,
                $schemaConstraint->referencingSchemaConstraint
            );
        }

        $items = $schema->getItems();

        if ($items !== null) {
            $constraints[] = new Items(
                $items,
                $schemaConstraint
            );
        }

        $contains = $schema->getContains();

        if ($contains !== null) {
            $minContains = $schema->getMinContains();
            $maxContains = $schema->getMaxContains();

            if ($minContains !== null) {
                $constraints[] = new MinContains(
                    $contains,
                    $minContains,
                    $schemaConstraint
                );
            } else {
                $constraints[] = new Contains(
                    $contains,
                    $schemaConstraint,
                    $schemaConstraint->referencingSchemaConstraint
                );
            }

            if ($maxContains !== null) {
                $constraints[] = new MaxContains(
                    $contains,
                    $maxContains,
                    $schemaConstraint
                );
            }
        }

        if ($schema->hasFormat()) {
            $constraints[] = new Format(
                $schema->getFormat(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration
            );
        }

        if ($schema->hasEnum()) {
            $constraints[] = new Enum(
                $schema->getEnum(),
                $schemaConstraint->schemaPath
            );
        }

        if ($schema->hasConst()) {
            $constraints[] = new Constant(
                $schema->getConst(),
                $schemaConstraint->schemaPath
            );
        }

        $if = $schema->getIf();

        if ($if !== null) {
            $constraints[] = new IfThenElse(
                $if,
                $schema->getThen(),
                $schema->getElse(),
                $schemaConstraint,
                $schemaConstraint->referencingSchemaConstraint
            );
        }

        if ($schema->hasDependentSchemas()) {
            $constraints[] = new DependentSchemas(
                $schema->getDependentSchemas(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint
            );
        }

        if ($schema->hasNot()) {
            $constraints[] = new Not(
                $schema->getNot(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration
            );
        }

        if ($schema->hasAnyOf()) {
            $constraints[] = new AnyOf(
                $schema->getAnyOf(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint->successfullyEvaluatedPaths
            );
        }

        $oneOf = $schema->getOneOf();

        if ($oneOf !== null) {
            $constraints[] = new OneOf(
                $oneOf,
                $schemaConstraint,
                $schemaConstraint->referencingSchemaConstraint
            );
        }

        if ($schema->hasAllOf()) {
            $constraints[] = new AllOf(
                $schema->getAllOf(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint->successfullyEvaluatedPaths
            );
        }

        if ($schema->hasRef()) {
            $constraints[] = new Schema(
                $schema->getResolvedReference(),
                // TODO: perhaps the path should be $schema->getRef()?
                "$schemaConstraint->schemaPath/\$ref",
                $schemaConstraint->configuration,
                $schemaConstraint,
                $schemaConstraint
            );
        }

        $dynamicRef = $schema->getDynamicRef();

        if ($dynamicRef !== null) {
            assert($this->context instanceof ExecutionContext);

            // TODO : should the exception be thrown here or rater during the resolvement process?
            $constraints[] = new Schema(
                $this->resolveDynamicReference($schema),
                // TODO: perhaps the path should be $schema->getRef()?
                "$schemaConstraint->schemaPath/\$dynamicRef",
                $schemaConstraint->configuration,
                $schemaConstraint,
                $schemaConstraint
            );
        }

        $unevaluatedItems = $schema->getUnevaluatedItems();

        if ($unevaluatedItems !== null) {
            $constraints[] = new UnevaluatedItems(
                $unevaluatedItems,
                $schemaConstraint,
                $schemaConstraint->referencingSchemaConstraint
            );
        }

        if ($schema->hasUnevaluatedProperties()) {
            $constraints[] = new UnevaluatedProperties(
                $schema->getUnevaluatedProperties(),
                $schemaConstraint->schemaPath,
                $schemaConstraint->configuration,
                $schemaConstraint->successfullyEvaluatedPaths,
                $schemaConstraint
            );
        }

        if ($schemaConstraint->configuration->stopOnFirstError) {
            return $constraints;
        }

        return empty($constraints) ? $constraints : [new Sequentially($constraints)];
    }

    private function markCurrentPathAsSuccessfullyEvaluated(Schema $constraint): void
    {
        $currentInstancePath = $this->getCurrentInstancePath();

        // TODO: perhaps not append but set? [path] => true, or [path] = [keyword_path, ..., keyword_pathN]
        $constraint->enclosingSchemaConstraint?->successfullyEvaluatedPaths->append($currentInstancePath);
        // TODO: verify nested $ref's
        $constraint->referencingSchemaConstraint?->successfullyEvaluatedPaths->append($currentInstancePath);
    }

    private function getCurrentInstancePath(): string
    {
        $propertyPath = $this->context->getPropertyPath();

        if ($propertyPath == '') {
            return $propertyPath;
        }

        $normalizePropertyPath = normalizePropertyPath($this->context->getPropertyPath());
        $normalizePropertyPathSegments = explode('/', $normalizePropertyPath);

        return $normalizePropertyPathSegments[array_key_last($normalizePropertyPathSegments)];
    }

    private function resolveDynamicReference(OAS\Schema $schema): OAS\Schema|bool
    {
        $dynamicReference = $schema->getDynamicReference();

        if ($dynamicReference instanceof OAS\Schema || is_bool($dynamicReference)) {
            return $dynamicReference;
        }

        assert(is_array($dynamicReference));
        assert($this->context instanceof ExecutionContext);
        $dynamicScope = $this->context->getDynamicScope();

        while (!empty($dynamicScope)) {
            $schemaResourceId = array_shift($dynamicScope);

            if (array_key_exists($schemaResourceId, $dynamicReference)) {
                return $dynamicReference[$schemaResourceId];
            }
        }

        throw new RuntimeException('TODO: throw a better exception');
    }
}
