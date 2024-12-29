<?php declare(strict_types=1);

use OAS\Resolver\Reference;
use OAS\Resolver\Uri;
use PHPUnit\Framework\TestCase;
use OAS\Resolver\Node;
use OAS\Resolver\Node\Factory;

// TODO: make more sophisticated :-)
class NodeTest extends TestCase
{
    private const URI = 'http://example.com/schema.json';

    /**
     * @test
     * @covers \OAS\Resolver\Node::find
     * @covers \OAS\Resolver\Node::getValue
     * @dataProvider graphProvider
     */
    public function itFindsNodeByPath(Node $node): void
    {
        $this->assertEquals(
            'string',
            $node->find('/properties/a.json/type')->getValue()
        );
        $this->assertEquals(
            'string',
            $node->find('/properties/a.json')->find('./type')->getValue()
        );
        $this->assertSame(
            $node,
            $node->find('.')
        );
        $this->assertSame(
            $node,
            $node->find('/properties')->find('..')
        );
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::getPath
     * @covers \OAS\Resolver\Node::nodesToRoot
     * @dataProvider graphProvider
     */
    public function itGetsPathFromRoot(Node $node): void
    {
        $this->assertEquals('/', $node->getPath());
        $this->assertEquals('/properties', $node['properties']->getPath());
        $this->assertEquals('/properties/a.json/type', $node['properties']['a.json']['type']->getPath());
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::getPathFromParent
     * @dataProvider graphProvider
     */
    public function itGetsPathFromParent(Node $node): void
    {
        $this->assertEquals('', $node->getPathFromParent());
        $this->assertEquals('properties', $node['properties']->getPathFromParent());
        $this->assertEquals('type', $node['properties']['a.json']['type']->getPathFromParent());
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::offsetExists
     * @covers \OAS\Resolver\Node::offsetGet
     * @dataProvider graphProvider
     */
    public function itImplementsArrayAccessInterface(Node $node): void
    {
        $this->assertInstanceOf(Node::class, $node['properties']['b']['type']);
        $this->assertEquals('number', $node['properties']['b']['type']->getValue());
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::getIterator
     * @covers \OAS\Resolver\Node::find
     * @covers \OAS\Resolver\Node::traverse
     * @dataProvider graphProvider
     */
    public function itImplementsIteratorInterface(Node $graph): void
    {
        foreach ($graph as $node) {
            self::assertTrue($node instanceof Node || $node instanceof Reference);
        }

        $iterator = $graph->getIterator();
        $this->assertSame($graph, $iterator->current());

        $iterator->next();
        $this->assertSame($graph->find('/properties'), $iterator->current());

        $iterator->next();
        $this->assertSame($graph->find('/properties/a.json'), $iterator->current());

        $iterator->next();
        $this->assertSame($graph->find('/properties/a.json/type'), $iterator->current());

        $iterator->next();
        $this->assertSame($graph->find('/properties/b'), $iterator->current());

        $iterator->next();
        $this->assertSame($graph->find('/properties/b/type'), $iterator->current());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::getUri
     * @dataProvider graphProvider
     */
    public function itGetsUri(Node $node): void
    {
        $this->assertEquals(self::URI, $node->getUri());
        $this->assertEquals(self::URI.'#/properties', $node['properties']->getUri());
    }

    /**
     * @test
     * @covers \OAS\Resolver\Node::denormalize
     */
    public function itConvertsGraphBackToArray(): void
    {
        $treeFactory = new Factory();

        $rawGraph = $this->getRawGraph();
        $graph = $treeFactory->create(
            $rawGraph,
            new Uri(self::URI)
        );

        $this->assertEquals($rawGraph, $graph->denormalize());
    }

    /** @return iterable<int, Node> */
    public function graphProvider(): iterable
    {
            yield [$this->getGraph()];
    }

    private function getGraph(): Node
    {
        $graphFactory = new Factory();

        return $graphFactory->create($this->getRawGraph(), new Uri(self::URI));
    }

    private function getRawGraph(): array
    {
        return [
            'properties' => [
                'a.json' => [
                    'type' => 'string'
                ],
                'b' => [
                    'type' => 'number'
                ]
            ]
        ];
    }
}
