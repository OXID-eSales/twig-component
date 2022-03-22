<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use OxidEsales\Twig\DependencyInjection\Compiler\TwigLoaderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;

final class TwigLoaderPassTest extends TestCase
{
    private ContainerBuilder $builder;
    private Definition $chainLoader;
    private TwigLoaderPass $pass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ContainerBuilder();
        $this->builder->register('twig');
        $this->chainLoader = new Definition('loader');
        $this->pass = new TwigLoaderPass();
    }

    public function testMapperPassWithOneTaggedLoader(): void
    {
        $this->builder->register('test_loader_1')
            ->addTag('twig.loader');

        $this->pass->process($this->builder);

        $this->assertSame('test_loader_1', (string) $this->builder->getAlias('twig.loader'));
    }

    public function testMapperPassWithTwoTaggedLoaders(): void
    {
        $this->builder->setDefinition('twig.loader.chain', $this->chainLoader);
        $this->builder->register('test_loader_1')
            ->addTag('twig.loader');
        $this->builder->register('test_loader_2')
            ->addTag('twig.loader');

        $this->pass->process($this->builder);

        $this->assertSame('twig.loader.chain', (string) $this->builder->getAlias('twig.loader'));
        $calls = $this->chainLoader->getMethodCalls();
        $this->assertCount(2, $calls);
        $this->assertEquals('addLoader', $calls[0][0]);
        $this->assertEquals('addLoader', $calls[1][0]);
        $this->assertEquals('test_loader_1', (string) $calls[0][1][0]);
        $this->assertEquals('test_loader_2', (string) $calls[1][1][0]);
    }

    public function testMapperPassWithTwoTaggedLoadersWithPriority(): void
    {
        $this->builder->setDefinition('twig.loader.chain', $this->chainLoader);
        $this->builder->register('test_loader_1')
            ->addTag('twig.loader', array('priority' => 100));
        $this->builder->register('test_loader_2')
            ->addTag('twig.loader', array('priority' => 200));

        $this->pass->process($this->builder);

        $this->assertSame('twig.loader.chain', (string) $this->builder->getAlias('twig.loader'));
        $calls = $this->chainLoader->getMethodCalls();
        $this->assertCount(2, $calls);
        $this->assertEquals('addLoader', $calls[0][0]);
        $this->assertEquals('addLoader', $calls[1][0]);
        $this->assertEquals('test_loader_2', (string) $calls[0][1][0]);
        $this->assertEquals('test_loader_1', (string) $calls[1][1][0]);
    }

    public function testMapperPassWithZeroTaggedLoaders(): void
    {
        $this->expectException(LogicException::class);
        $this->pass->process($this->builder);
    }
}
