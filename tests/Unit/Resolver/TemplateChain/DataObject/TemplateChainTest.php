<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain\DataObject;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use PHPUnit\Framework\TestCase;

class TemplateChainTest extends TestCase
{
    private TemplateChain $templateChain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateChain = new TemplateChain();
    }

    public function testAppendAddsTemplateToChain(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);

        $this->assertEquals(1, $this->templateChain->count());
        $this->assertTrue($this->templateChain->has($template));
    }

    public function testAppendWithParentNamespaceInsertsBeforeParent(): void
    {
        $parent = $this->createMockTemplate('parent', 'parent::template.twig');
        $child = $this->createMockTemplate('child', 'child::template.twig', 'parent');

        $this->templateChain->append($parent);
        $this->templateChain->append($child);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertSame($child, $this->templateChain->getLastChild());
    }

    public function testAppendWithNonExistentParentAppendsToEnd(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig', 'nonexistent');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $this->assertEquals(2, $this->templateChain->count());
    }

    public function testRemoveDeletesTemplateFromChain(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);
        $this->assertTrue($this->templateChain->has($template));

        $this->templateChain->remove($template);
        $this->assertFalse($this->templateChain->has($template));
        $this->assertEquals(0, $this->templateChain->count());
    }

    public function testRemoveNonExistentTemplateDoesNothing(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->remove($template2);

        $this->assertEquals(1, $this->templateChain->count());
        $this->assertTrue($this->templateChain->has($template1));
    }

    public function testAppendChainMergesMultipleTemplates(): void
    {
        $chain2 = new TemplateChain();

        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $chain2->append($template2);

        $this->templateChain->appendChain($chain2);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertTrue($this->templateChain->has($template1));
        $this->assertTrue($this->templateChain->has($template2));
    }

    public function testHasModuleIdReturnsTrueWhenModuleExists(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);

        $this->assertTrue($this->templateChain->hasModuleId('module1'));
        $this->assertFalse($this->templateChain->hasModuleId('module2'));
    }

    public function testGetByModuleIdReturnsCorrectTemplate(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $this->assertSame($template1, $this->templateChain->getByModuleId('module1'));
        $this->assertSame($template2, $this->templateChain->getByModuleId('module2'));
        $this->assertNull($this->templateChain->getByModuleId('module3'));
    }

    public function testGetParentReturnsNextTemplateInChain(): void
    {
        $child = $this->createMockTemplate('child', 'child::template.twig');
        $parent = $this->createMockTemplate('parent', 'parent::template.twig');

        $this->templateChain->append($child);
        $this->templateChain->append($parent);

        $this->assertSame($parent, $this->templateChain->getParent($child));
    }

    public function testGetParentReturnsNullForLastTemplate(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);

        $this->assertNull($this->templateChain->getParent($template));
    }

    public function testGetLastChildReturnsFirstTemplate(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $this->assertSame($template1, $this->templateChain->getLastChild());
    }

    public function testHasParentReturnsTrueWhenNotLastInChain(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $this->assertTrue($this->templateChain->hasParent($template1));
        $this->assertFalse($this->templateChain->hasParent($template2));
    }

    public function testCountReturnsCorrectNumber(): void
    {
        $this->assertEquals(0, $this->templateChain->count());

        $this->templateChain->append($this->createMockTemplate('module1', 'module1::template.twig'));
        $this->assertEquals(1, $this->templateChain->count());

        $this->templateChain->append($this->createMockTemplate('module2', 'module2::template.twig'));
        $this->assertEquals(2, $this->templateChain->count());
    }

    public function testGetIteratorAllowsIteration(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $collected = [];
        foreach ($this->templateChain as $template) {
            $collected[] = $template;
        }

        $this->assertCount(2, $collected);
        $this->assertSame($template1, $collected[0]);
        $this->assertSame($template2, $collected[1]);
    }

    public function testComplexParentChildHierarchy(): void
    {
        $grandparent = $this->createMockTemplate('grandparent', 'grandparent::template.twig');
        $parent = $this->createMockTemplate('parent', 'parent::template.twig', 'grandparent');
        $child = $this->createMockTemplate('child', 'child::template.twig', 'parent');

        $this->templateChain->append($grandparent);
        $this->templateChain->append($parent);
        $this->templateChain->append($child);

        $this->assertEquals(3, $this->templateChain->count());
        $this->assertSame($child, $this->templateChain->getLastChild());
        $this->assertSame($parent, $this->templateChain->getParent($child));
        $this->assertSame($grandparent, $this->templateChain->getParent($parent));
    }

    public function testAppendMultipleChildrenToSameParent(): void
    {
        $parent = $this->createMockTemplate('parent', 'parent::template.twig');
        $child1 = $this->createMockTemplate('child1', 'child1::template.twig', 'parent');
        $child2 = $this->createMockTemplate('child2', 'child2::template.twig', 'parent');

        $this->templateChain->append($parent);
        $this->templateChain->append($child1);
        $this->templateChain->append($child2);

        $this->assertEquals(3, $this->templateChain->count());
        $this->assertSame($child1, $this->templateChain->getLastChild());
    }

    public function testHasReturnsFalseForNonExistentTemplate(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);

        $this->assertTrue($this->templateChain->has($template1));
        $this->assertFalse($this->templateChain->has($template2));
    }

    public function testGetParentForNonExistentTemplateReturnsNull(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);

        $this->assertNull($this->templateChain->getParent($template2));
    }

    public function testAppendEmptyChainDoesNothing(): void
    {
        $chain2 = new TemplateChain();
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);
        $this->templateChain->appendChain($chain2);

        $this->assertEquals(1, $this->templateChain->count());
    }

    public function testRemoveFromMiddleOfChain(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');
        $template3 = $this->createMockTemplate('module3', 'module3::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);
        $this->templateChain->append($template3);

        $this->templateChain->remove($template2);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertTrue($this->templateChain->has($template1));
        $this->assertFalse($this->templateChain->has($template2));
        $this->assertTrue($this->templateChain->has($template3));
    }

    public function testIteratorWithEmptyChain(): void
    {
        $collected = [];
        foreach ($this->templateChain as $template) {
            $collected[] = $template;
        }

        $this->assertCount(0, $collected);
    }

    public function testHasParentWithSingleTemplate(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);

        $this->assertFalse($this->templateChain->hasParent($template));
    }

    public function testAppendWithParentNamespaceButParentNotInChain(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig', 'nonexistent');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertSame($template1, $this->templateChain->getLastChild());
    }

    public function testMultipleRemovalsAndAppends(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);
        $this->templateChain->append($template2);
        $this->templateChain->remove($template1);
        $this->templateChain->append($template1);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertTrue($this->templateChain->has($template1));
        $this->assertTrue($this->templateChain->has($template2));
    }

    public function testGetByModuleIdWithEmptyChain(): void
    {
        $this->assertNull($this->templateChain->getByModuleId('nonexistent'));
        $this->assertFalse($this->templateChain->hasModuleId('nonexistent'));
    }

    public function testAppendChainWithParentChildRelationships(): void
    {
        $chain2 = new TemplateChain();

        $parent = $this->createMockTemplate('parent', 'parent::template.twig');
        $child = $this->createMockTemplate('child', 'child::template.twig', 'parent');

        $this->templateChain->append($parent);
        $chain2->append($child);

        $this->templateChain->appendChain($chain2);

        $this->assertEquals(2, $this->templateChain->count());
        $this->assertSame($child, $this->templateChain->getLastChild());
        $this->assertSame($parent, $this->templateChain->getParent($child));
    }

    public function testInsertionOrderWithMultipleLevels(): void
    {
        $root = $this->createMockTemplate('root', 'root::template.twig');
        $middle = $this->createMockTemplate('middle', 'middle::template.twig', 'root');
        $leaf = $this->createMockTemplate('leaf', 'leaf::template.twig', 'middle');

        $this->templateChain->append($root);
        $this->templateChain->append($leaf);
        $this->templateChain->append($middle);

        $this->assertEquals(3, $this->templateChain->count());

        $items = iterator_to_array($this->templateChain);
        $this->assertSame($middle, $items[0]);
        $this->assertSame($root, $items[1]);
        $this->assertSame($leaf, $items[2]);
    }

    public function testHasParentForTemplateNotInChain(): void
    {
        $template1 = $this->createMockTemplate('module1', 'module1::template.twig');
        $template2 = $this->createMockTemplate('module2', 'module2::template.twig');

        $this->templateChain->append($template1);

        $this->assertTrue($this->templateChain->hasParent($template2));
    }

    public function testRemoveSameTemplateMultipleTimes(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);
        $this->templateChain->remove($template);
        $this->templateChain->remove($template);

        $this->assertEquals(0, $this->templateChain->count());
    }

    public function testAppendSameTemplateMultipleTimes(): void
    {
        $template = $this->createMockTemplate('module1', 'module1::template.twig');

        $this->templateChain->append($template);
        $this->templateChain->append($template);

        $this->assertEquals(2, $this->templateChain->count());
    }

    public function testEmptyChainOperations(): void
    {
        $this->assertEquals(0, $this->templateChain->count());
        $this->assertFalse($this->templateChain->hasModuleId('any'));
        $this->assertNull($this->templateChain->getByModuleId('any'));

        $template = $this->createMockTemplate('module1', 'module1::template.twig');
        $this->assertFalse($this->templateChain->has($template));
        $this->assertNull($this->templateChain->getParent($template));
    }

    private function createMockTemplate(
        string $namespace,
        string $fullyQualifiedName,
        string $parentNamespace = ''
    ): TemplateTypeInterface {
        $mock = $this->createMock(TemplateTypeInterface::class);

        $mock->method('getNamespace')
            ->willReturn($namespace);

        $mock->method('getFullyQualifiedName')
            ->willReturn($fullyQualifiedName);

        $mock->method('getParentNamespace')
            ->willReturn($parentNamespace);

        return $mock;
    }
}
