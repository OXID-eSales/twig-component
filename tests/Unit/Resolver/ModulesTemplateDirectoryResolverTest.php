<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ModulesTemplateDirectoryResolverTest extends TestCase
{
    private const SHOP_ID = 1;

    private ActiveModulesDataProviderInterface $activeModulesDataProvider;
    private ModulePathResolverInterface $modulePathResolver;
    private Filesystem $filesystem;
    private ModulesTemplateDirectoryResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeModulesDataProvider = $this->createStub(ActiveModulesDataProviderInterface::class);
        $this->modulePathResolver = $this->createStub(ModulePathResolverInterface::class);
        $this->filesystem = $this->createStub(Filesystem::class);

        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getDefaultShopId')->willReturn(self::SHOP_ID);

        $this->resolver = new ModulesTemplateDirectoryResolver(
            $this->activeModulesDataProvider,
            $this->modulePathResolver,
            $context,
            $this->filesystem,
        );
    }

    public function testReturnsEmptyWhenNoActiveModules(): void
    {
        $this->activeModulesDataProvider->method('getModuleIds')->willReturn([]);

        $this->assertEmpty($this->resolver->getTemplateDirectories());
    }

    public function testSkipsModulesWithoutExistingTemplateDir(): void
    {
        $this->activeModulesDataProvider->method('getModuleIds')->willReturn([
            'module-without-templates',
            'module-with-templates',
        ]);
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')->willReturnMap([
            ['module-without-templates', self::SHOP_ID, '/path/to/module-without-templates'],
            ['module-with-templates', self::SHOP_ID, '/path/to/module-with-templates'],
        ]);
        $this->filesystem->method('exists')->willReturnMap([
            ['/path/to/module-without-templates/views/twig', false],
            ['/path/to/module-with-templates/views/twig', true],
        ]);

        $directories = $this->resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame('module-with-templates', $directories[0]->getNamespace());
        $this->assertSame('/path/to/module-with-templates/views/twig', $directories[0]->getDirectory());
    }

    public function testReturnsAllMatchingDirectoriesInModuleOrder(): void
    {
        $this->activeModulesDataProvider->method('getModuleIds')->willReturn([
            'first-module',
            'second-module',
            'third-module',
        ]);
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')->willReturnMap([
            ['first-module', self::SHOP_ID, '/modules/first'],
            ['second-module', self::SHOP_ID, '/modules/second'],
            ['third-module', self::SHOP_ID, '/modules/third'],
        ]);
        $this->filesystem->method('exists')->willReturn(true);

        $directories = $this->resolver->getTemplateDirectories();

        $this->assertCount(3, $directories);
        $this->assertSame('first-module', $directories[0]->getNamespace());
        $this->assertSame('second-module', $directories[1]->getNamespace());
        $this->assertSame('third-module', $directories[2]->getNamespace());
    }
}
