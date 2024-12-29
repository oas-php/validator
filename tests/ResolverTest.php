<?php declare(strict_types=1);

use OAS\Resolver\Resolver;
use PHPUnit\Framework\TestCase;
use OAS\Resolver\Node;
use OAS\Resolver\Reference;
use function OAS\Resolver\jsonPointerDecode;
use function OAS\Resolver\pathSegments;

// TODO: reFUCKtor it!
class ResolverTest extends TestCase
{
    /**
     * @test
     * @covers \OAS\Resolver\Resolver::resolve
     * @covers \OAS\Resolver\Resolver::resolveDecoded
     * @covers \OAS\Resolver\Node::find
     * @covers \OAS\Resolver\Node::denormalize
     * @dataProvider dataProvider
     */
    public function itResolvesRefsCorrectly(Node $resolved, array $assertions): void
    {
        ['resolved' => $referencesAssertions, 'denormalized' => $denormalizedAssertions] = $assertions;

//        assert(count($referencesAssertions) > 0);
//        assert(count($denormalizedAssertions) > 0);

        foreach ($referencesAssertions as $referencesAssertion) {
            ['path' => $referencePath, 'value' => $value] = $referencesAssertion;
            $reference = $resolved->find($referencePath);

            self::assertInstanceOf(Reference::class, $reference);
            self::assertEquals($reference->getValue(), $value);
        }

        $denormalized = $resolved->denormalize();

        foreach ($denormalizedAssertions as $denormalizedAssertion) {
            ['path' => $path, 'value' => $denormalizedPart] = $denormalizedAssertion;
            self::assertEquals($denormalizedPart, retrieveByPath($denormalized, $path));
        }
    }

    /**
     * @return iterable{resolved: Node, assertions: array}
     */
    public function dataProvider(): iterable
    {
        $resolver = new Resolver;

        yield [
            'resolved' => $resolver->resolveDecoded(
                [
                    'a.json' => [
                        '$ref' => '#/defs/a.json'
                    ],
                    'defs' => [
                        'a.json' => true
                    ]
                ]
            ),
            'assertions' => [
                'resolved' => [
                    [
                        'path' => '/a.json/$ref',
                        'value' => true
                    ]
                ],
                'denormalized' => [
                    [
                        'path' => '',
                        'value' => [
                            'a.json' => [
                                '$ref' => '#/defs/a.json'
                            ],
                            'defs' => [
                                'a.json' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        yield [
            'resolved' => $resolved = $resolver->resolveDecoded(
                [
                    'a.json' => [
                        '$ref' => '#/'
                    ]
                ]
            ),
            'assertions' => [
                'resolved' => [
                    [
                        'path' => '/a.json/$ref',
                        'value' => $resolved->getValue()
                    ]
                ],
                'denormalized' => [
                    [
                        'path' => '',
                        'value' => [
                            'a.json' => [
                                '$ref' => '#/'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        yield [
            'resolved' => $resolved = $resolver->resolveDecoded(
                [
                    'a.json' => [
                        '$ref' => '#/b'
                    ],
                    'b' => [
                        '$ref' => '#/c'
                    ],
                    'c' => [
                        '$ref' => '#/b'
                    ]
                ]
            ),
            'assertions' => [
                'resolved' => [
                    [
                        'path' => '/a.json/$ref',
                        'value' => $resolved['b']->getValue()
                    ],
                    [
                        'path' => '/b/$ref',
                        'value' => $resolved['c']->getValue()
                    ],
                    [
                        'path' => '/c/$ref',
                        'value' =>$resolved['b']->getValue()
                    ],
//                    [
//                        'path' => '/b/$ref/$ref',
//                        'value' =>$resolved['b']->getValue()
//                    ],
//                    [
//                        'path' => '/c/$ref/$ref',
//                        'value' => $resolved['c']->getValue()
//                    ]
                ],
                'denormalized' => [
                    [
                        'path' => '',
                        'value' => [
                            'a.json' => [
                                '$ref' => '#/b'
                            ],
                            'b' => [
                                '$ref' => '#/c'
                            ],
                            'c' => [
                                '$ref' => '#/b'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        yield [
            'resolved' => $resolved = $resolver->resolveDecoded(
                [
                    'a.json' => [
                        'b' => [
                            '$ref' => '#/c'
                        ]
                    ],
                    'c' => [
                        '$ref' => '#/a.json'
                    ]
                ]
            ),
            'assertions' => [
                'resolved' => [
                    [
                        'path' => '/a.json/b/$ref',
                        'value' => $resolved['c']->getValue()
                    ],
//                    [
//                        'path' => '/a.json/b/$ref/$ref',
//                        'value' => $resolved['a.json']->getValue()
//                    ],
                    [
                        'path' => '/c/$ref',
                        'value' => $resolved['a.json']->getValue()
                    ]
                ],
                'denormalized' => [
                    [
                        'path' => '/c',
                        'value' => [
                            '$ref' => '#/a.json'
                        ]
                    ]
                ]
            ]
        ];
    }
}

function retrieveByPath($graph, string $path)
{
    $current = &$graph;
    $path = array_map(
        fn (string $segment) => jsonPointerDecode($segment),
        pathSegments($path)
    );

    foreach ($path as $pathSegment) {
        if (!array_key_exists($pathSegment, $current)) {
            throw new \RuntimeException(sprintf('Path "%s" does not exist', \join(' -> ', $path)));
        }

        $current = &$current[$pathSegment];
    }

    return $current;
}
