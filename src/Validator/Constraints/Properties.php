<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use Symfony\Component\Validator\Constraints\Collection as BaseCollection;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class Properties extends BaseCollection
{
    /**
     * @param array<string, OAS\Schema> $properties
     */
    public function __construct(
        array $properties,
        string $path,
        Configuration $configuration,
        Schema $enclosingSchemaConstraint,
        ?Schema $referencingSchemaConstraint = null
    )
    {
        $propertyNames = array_keys($properties);

        parent::__construct([
            'fields' => array_combine(
                $propertyNames,
                array_map(
                    fn (string $propertyName, OAS\Schema|bool $schema) => new Required(
                        new Schema(
                            $schema,
                            "{$path}/properties/{$propertyName}",
                            $configuration,
                            $enclosingSchemaConstraint,
                            $referencingSchemaConstraint
                        )
                    ),
                    $propertyNames,
                    $properties
                )
            ),
            'allowMissingFields' => true,
            'allowExtraFields' => true,
            'groups' => ['Default']
        ]);
    }
}
