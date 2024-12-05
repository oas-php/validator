<?php declare(strict_types=1);

use OAS\Resolver;
use OAS\Schema;
use OAS\Schema\Factory;
use OAS\Validator\Symfony\Validator;
use OAS\Validator\SchemaConformanceFailure;
use PHPUnit\Framework\TestCase;
use function iter\{toArray, flatten};

class ValidatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider withoutViolationsDraft2020_12_DataProvider
     */
    public function draft2020_20OfficialTestSuite($instance, bool|stdClass $rawSchema, bool $valid, string $testId): void
    {
//        try {
            // TODO: with resolver the $ref property is not set sometimes
            // e.g. here: (unevaluatedProperties with $ref: with no unevaluated properties (filename: unevaluatedProperties.json))
            $schema = (new Factory(new Resolver()))->createFromPrimitives($rawSchema);
            $this->assertValidatorWorksProperly($instance, $schema, $valid, $testId);
//        } catch (Exception $e) {
//            $this->fail(
//                sprintf(
//                    "Could not create schema from provided properties: \n\n%s\n\nReason: %s\n\nTest reference: %s",
//                    json_encode($rawSchema, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK),
//                    $e->getMessage(),
//                    $testId
//                )
//            );
//        }
    }

    /**
     * test
     * @dataProvider withoutViolationsDataProvider
     */
    public function draft2019_19OfficialTestSuite($instance, Schema $schema, bool $valid, string $testId): void
    {
        $this->assertValidatorWorksProperly($instance, $schema, $valid, $testId);
    }

    /**
     * test
     * @dataProvider withViolationsDataProvider
     */
    public function draft2019_19AdditionalSuiteWithViolations($instance, Schema $schema, array $violations, string $testId): void
    {
        $this->assertValidatorWorksProperly($instance, $schema, $violations, $testId);
    }

    /**
     * @param Schema|bool $schema   the schema that instance will be checked against
     * @param mixed $instance       the instance being validated
     * @param array|boolean $result when boolean it indicates whether instance is valid or not
     *                              when array it holds schema constraints violations
     *                              (if empty - instance conforms to schema)
     * @param string $testRef       test ref formatted as: "{{schema-description}}: {{test-description}} (filename: {{filename}})"
     */
    private function assertValidatorWorksProperly(
        mixed $instance,
        Schema|bool $schema,
        array|bool $result,
        string $testRef
    ): void
    {
        $isValid = is_bool($result) ? $result : empty($result);

        try {
            (new Validator)->validate($instance, $schema);

            if ($isValid) {
                $this->markAsPassed();
            } else {
                $this->fail(
                    sprintf(
                        "Instance is valid against schema but should not be (%s).\n\n%s",
                        $testRef,
                        $this->printDetails($instance, $schema)
                    )
                );
            }
        } catch (SchemaConformanceFailure $exception) {
            if (!$isValid) {
                if (!is_bool($result)) {
                    $this->assertCorrectViolationsRaised(
                        $instance,
                        $schema,
                        $result,
                        $exception,
                        $testRef
                    );
                }

                $this->markAsPassed();
            } else {
                $this->fail(
                    sprintf(
                        "Instance is not valid against schema but it should be (%s).\n\n%s",
                        $testRef,
                        $this->printDetails($instance, $schema)
                    )
                );
            }
        }
    }

    public function withViolationsDataProvider(): array
    {
        return $this->readTestSuites(
            __DIR__ . '/suites/draft2019-09-with-violations',
        );
    }

    public function withoutViolationsDataProvider(): array
    {
        return $this->readTestSuites(
            __DIR__ . '/suites/draft2019-09-bckp',
//            fn (string $fileName) => !in_array(
//                $fileName,
//                [
//                    __DIR__ . '/suites/draft2019-09/anchor.json',
//                    __DIR__ . '/suites/draft2019-09/content.json',
//                    __DIR__ . '/suites/draft2019-09/default.json',
//                    __DIR__ . '/suites/draft2019-09/defs.json',
//                    __DIR__ . '/suites/draft2019-09/format.json',
//                    __DIR__ . '/suites/draft2019-09/id.json',
//                    __DIR__ . '/suites/draft2019-09/ref.json',
//                    __DIR__ . '/suites/draft2019-09/recursiveRef.json',
//                    __DIR__ . '/suites/draft2019-09/ref-fail.json',
//                    __DIR__ . '/suites/draft2019-09/refRemote.json',
//                    __DIR__ . '/suites/draft2019-09/unevaluatedItems.json',
//                    __DIR__ . '/suites/draft2019-09/unevaluatedProperties.json',
//                    __DIR__ . '/suites/draft2019-09/optional/bignum.json',
//                    __DIR__ . '/suites/draft2019-09/optional/refOfUnknownKeyword.json',
//                    __DIR__ . '/suites/draft2019-09/optional/float-overflow.json',
//                    __DIR__ . '/suites/draft2019-09/optional/non-bmp-regex.json',
//                    __DIR__ . '/suites/draft2019-09/optional/ecmascript-regex.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/date-time.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/date.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/email.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/uuid.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/regex.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/relative-json-pointer.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/json-pointer.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/duration.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/idn-hostname.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/uri.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/iri.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/idn-email.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/time.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/iri-reference.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/uri-template.json',
//                    __DIR__ . '/suites/draft2019-09/optional/format/uri-reference.json'
//                ]
//            )
        );
    }

    public function withoutViolationsDraft2020_12_DataProvider(): array
    {
        // 917
        return $this->readTestSuites(
            __DIR__ . '/suites/draft2020-12',
            fn (string $filename) => in_array(
                $filename, [
//                    __DIR__ . '/suites/draft2020-12/format.json',
//                    __DIR__ . '/suites/draft2020-12/defs.json',
//                    __DIR__ . '/suites/draft2020-12/dynamicRef.json', // 13 / 3
//                    __DIR__ . '/suites/draft2020-12/ref.json', // 6 errors
                    __DIR__ . '/suites/draft2020-12/refRemote.json', // 15 errors
//                    __DIR__ . '/suites/draft2020-12/content.json',
//                    __DIR__ . '/suites/draft2020-12/vocabulary.json',
//
//                    __DIR__ . '/suites/draft2020-12/anchor.json',
//                    __DIR__ . '/suites/draft2020-12/boolean_schema.json',
//                    __DIR__ . '/suites/draft2020-12/default.json',
//                    __DIR__ . '/suites/draft2020-12/infinite-loop-detection.json',
//                    __DIR__ . '/suites/draft2020-12/allOf.json',
//                    __DIR__ . '/suites/draft2020-12/anyOf.json',
//                    __DIR__ . '/suites/draft2020-12/oneOf.json',
//                    __DIR__ . '/suites/draft2020-12/not.json',
//                    __DIR__ . '/suites/draft2020-12/if-then-else.json',
//                    __DIR__ . '/suites/draft2020-12/dependentSchemas.json',
//                    __DIR__ . '/suites/draft2020-12/prefixItems.json',
//                    __DIR__ . '/suites/draft2020-12/items.json',
//                    __DIR__ . '/suites/draft2020-12/contains.json',
//                    __DIR__ . '/suites/draft2020-12/properties.json',
//                    __DIR__ . '/suites/draft2020-12/patternProperties.json',
//                    __DIR__ . '/suites/draft2020-12/additionalProperties.json',
//                    __DIR__ . '/suites/draft2020-12/propertyNames.json',
//                    __DIR__ . '/suites/draft2020-12/type.json',
//                    __DIR__ . '/suites/draft2020-12/enum.json',
//                    __DIR__ . '/suites/draft2020-12/const.json',
//                    __DIR__ . '/suites/draft2020-12/multipleOf.json',
//                    __DIR__ . '/suites/draft2020-12/maximum.json',
//                    __DIR__ . '/suites/draft2020-12/exclusiveMaximum.json',
//                    __DIR__ . '/suites/draft2020-12/minimum.json',
//                    __DIR__ . '/suites/draft2020-12/exclusiveMinimum.json',
//                    __DIR__ . '/suites/draft2020-12/maxLength.json',
//                    __DIR__ . '/suites/draft2020-12/minLength.json',
//                    __DIR__ . '/suites/draft2020-12/pattern.json',
//                    __DIR__ . '/suites/draft2020-12/maxItems.json',
//                    __DIR__ . '/suites/draft2020-12/minItems.json',
//                    __DIR__ . '/suites/draft2020-12/uniqueItems.json',
//                    __DIR__ . '/suites/draft2020-12/maxContains.json',
//                    __DIR__ . '/suites/draft2020-12/minContains.json',
//                    __DIR__ . '/suites/draft2020-12/maxProperties.json',
//                    __DIR__ . '/suites/draft2020-12/minProperties.json',
//                    __DIR__ . '/suites/draft2020-12/required.json',
//                    __DIR__ . '/suites/draft2020-12/dependentSchemas.json',
//                    __DIR__ . '/suites/draft2020-12/unevaluatedProperties.json',
//                    __DIR__ . '/suites/draft2020-12/unevaluatedItems.json'
                ]
            )
        );
    }

    public function readTestSuites(string $dir, callable $filter = null): array
    {
        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir)
            ),
            '/^.+\.json/i',
            RecursiveRegexIterator::GET_MATCH
        );

        $files = iter\toArray(
            iter\map(fn (array $wrapped) => $wrapped[0], $iterator)
        );

        return toArray(
            flatten(
                array_map(
                    fn (string $fileName) => array_map(
                        fn ($suite) => array_map(
                            fn (stdClass $test) => [
                                $test->data,
                                $suite->schema,
                                $test->valid ?? $test->violations,
                                sprintf(
                                    "%s: %s (filename: %s)",
                                    $suite->description,
                                    $test->description,
                                    basename($fileName)
                                )
                            ],
                            (array) $suite->tests
                        ),
                        (array) json_decode(
                            file_get_contents($fileName)
                        )
                    ),
                    $filter ? array_filter($files, $filter) : $files
                ),
                2
            )
        );
    }

    private function printDetails($instance, Schema $schema): string
    {
        return sprintf(
            "Instance: \n%s \n\nSchema: \n%s",
            json_encode($instance, JSON_PRETTY_PRINT),
            json_encode($schema, JSON_PRETTY_PRINT)
        );
    }

    private function assertCorrectViolationsRaised(
        $instance,
        Schema $schema,
        array $expectedViolations,
        SchemaConformanceFailure $exception,
        string $testRef
    ): void {
        /** @var Validator\ConstraintViolation $violation */
        foreach ($exception->getViolations() as $violation) {
            if (empty($expectedViolations)) {
                $this->fail('Validator detected more violations than expected');
            }

            $violations = array_shift($expectedViolations);
            array_push($violations, null);

            [$schemaPath, $instancePath, $violationCode] = $violations;

            $this->assertEquals(
                $schemaPath,
                $violation->getSchemaPath(),
                sprintf(
                    "Invalid schema path\n\n%s",
                    $this->printDetails($instance, $schema)
                )
            );

            $this->assertEquals(
                $instancePath,
                $violation->getInstancePath(),
                sprintf(
                    "Invalid instance path\n\n%s",
                    $this->printDetails($instance, $schema)
                )
            );

            if (!is_null($violationCode)) {
                $resolvedViolationCode = constant("OAS\\Validator\\Constraints\\{$violationCode}");

                $this->assertEquals(
                    $resolvedViolationCode,
                    $violation->getCode(),
                    sprintf(
                        "Unexpected violation code\n\n%s",
                        $this->printDetails($instance, $schema)
                    )
                );

            }
        }
    }

    private function markAsPassed(): void
    {
        $this->assertTrue(true);
    }
}
