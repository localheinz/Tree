<?php
/*
 * This file is part of Tree.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tree\Test\Builder;

use PHPUnit\Framework;
use Tree\Node\Node;
use Tree\Builder\NodeBuilder;

/**
 * Unit tests for class FirstClass
 */
class NodeTest extends Framework\TestCase
{
    /** @var NodeBuilder */
    protected $builder;

    public function setUp()
    {
        $this->builder = new NodeBuilder;
    }

    public function testConstructorCreatesEmptyNodeIfNoSpecified()
    {
        $builder = new NodeBuilder;

        $this->assertNull($builder->getNode()->getValue());
    }

    public function testConstructor()
    {
        $builder = new NodeBuilder($node = new Node('node'));

        $this->assertSame($node, $builder->getNode());
    }

    public function testSetNodeAndGetNode()
    {
        $this->builder->setNode($node1 = new Node('node1'));
        $this->assertSame($node1, $this->builder->getNode());

        $this->builder->setNode($node2 = new Node('node2'));
        $this->assertSame($node2, $this->builder->getNode());
    }

    public function testLeaf()
    {
        $this->builder->leaf('a')->leaf('b');

        $children = $this->builder->getNode()->getChildren();

        $this->assertSame('a', $children[0]->getValue());
        $this->assertSame('b', $children[1]->getValue());
    }

    public function testLeafs()
    {
        $this->builder->leafs('a', 'b');

        $children = $this->builder->getNode()->getChildren();

        $this->assertSame('a', $children[0]->getValue());
        $this->assertSame('b', $children[1]->getValue());
    }

    public function testTreeAddNewNodeAsChildOfTheParentNode()
    {
        $this->builder
            ->value('root')
            ->tree('a')
                ->tree('b')->end()
                ->leaf('c')
            ->end()
        ;

        $node = $this->builder->getNode();
        $this->assertSame(array('a'), $this->childrenValues($node->getChildren()));

        $subtree = $node->getChildren()[0];
        $this->assertSame(array('b', 'c'), $this->childrenValues($subtree->getChildren()));
    }

    public function testTree()
    {
        $this->builder->tree('a')->tree('b');

        $this->assertSame('b', $this->builder->getNode()->getValue());
    }

    public function testEnd()
    {
        $this->builder
            ->value('root')
            ->tree('a')
                ->tree('b')
                    ->tree('c')
                    ->end();

        $this->assertSame('b', $this->builder->getNode()->getValue());

        $this->builder->end();
        $this->assertSame('a', $this->builder->getNode()->getValue());

        $this->builder->end();
        $this->assertSame('root', $this->builder->getNode()->getValue());
    }

    public function testValue()
    {
        $this->builder->value('foo')->value('bar');

        $this->assertSame('bar', $this->builder->getNode()->getValue());
    }

    public function testNodeInstanceByValue()
    {
        $node = $this->builder->nodeInstanceByValue('baz');

        $this->assertSame('baz', $node->getValue());
        $this->assertInstanceOf('Tree\Node\Node', $node);
    }

    /**
     * @param array[Node] $children
     * @return array
     */
    private function childrenValues(array $children)
    {
        return array_map(function(Node $node) {
            return $node->getValue();
        }, $children);
    }
}
