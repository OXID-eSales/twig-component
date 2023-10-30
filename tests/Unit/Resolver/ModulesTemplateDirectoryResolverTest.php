<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Filesystem;

final class ModulesTemplateDirectoryResolverTest extends TestCase
{
    use ProphecyTrait;

    private ModulePathResolverInterface|ObjectProphecy $modulePathResolver;
    private ObjectProphecy|ContextInterface $context;
    private ObjectProphecy|ActiveModulesDataProviderInterface $activeModulesDataProvider;
    private Filesystem|ObjectProphecy $filesystem;
    private $shopId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareMocks();
    }

    public function testGetTemplateDirectoriesWith0Modules(): void
    {
        $this->activeModulesDataProvider->getModuleIds()->willReturn([]);

        $directories = $this->getDirectoryResolver()->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWith1ModuleAnd0ExistingTemplates(): void
    {
        $moduleId1 = 'module-1';
        $modulePath1 = 'module-path-1';
        $moduleTemplateDirectory1 = "$modulePath1/views/twig";

        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId1, $this->shopId)
            ->willReturn($modulePath1);

        $this->activeModulesDataProvider->getModuleIds()->willReturn([$moduleId1]);

        $this->filesystem->exists($moduleTemplateDirectory1)->willReturn(false);

        $directories = $this->getDirectoryResolver()->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWith1ModuleAnd1ExistingTemplate(): void
    {
        $moduleId1 = 'module-1';
        $modulePath1 = 'module-path-1';
        $moduleTemplateDirectory1 = "$modulePath1/views/twig";

        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId1, $this->shopId)
            ->willReturn($modulePath1);

        $this->activeModulesDataProvider->getModuleIds()->willReturn([$moduleId1]);

        $this->filesystem->exists($moduleTemplateDirectory1)->willReturn(true);

        $directories = $this->getDirectoryResolver()->getTemplateDirectories();

        $this->assertEquals($moduleTemplateDirectory1, $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesWith2ModulesAnd2ExistingTemplates(): void
    {
        $moduleId1 = 'module-1';
        $moduleId2 = 'module-2';
        $modulePath1 = 'module-path-1';
        $modulePath2 = 'module-path-2';
        $moduleTemplateDirectory1 = "$modulePath1/views/twig";
        $moduleTemplateDirectory2 = "$modulePath2/views/twig";

        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId1, $this->shopId)
            ->willReturn($modulePath1);
        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId2, $this->shopId)
            ->willReturn($modulePath2);

        $this->activeModulesDataProvider->getModuleIds()->willReturn([$moduleId1, $moduleId2]);

        $this->filesystem->exists($moduleTemplateDirectory1)->willReturn(true);
        $this->filesystem->exists($moduleTemplateDirectory2)->willReturn(true);

        $directories = $this->getDirectoryResolver()->getTemplateDirectories();

        $this->assertCount(2, $directories);
    }

    public function testGetTemplateDirectoriesWith3ModulesAnd1ExistingTemplate(): void
    {
        $moduleId1 = 'module-1';
        $moduleId2 = 'module-2';
        $moduleId3 = 'module-3';
        $modulePath1 = 'module-path-1';
        $modulePath2 = 'module-path-2';
        $modulePath3 = 'module-path-3';
        $moduleTemplateDirectory1 = "$modulePath1/views/twig";
        $moduleTemplateDirectory2 = "$modulePath2/views/twig";
        $moduleTemplateDirectory3 = "$modulePath3/views/twig";

        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId1, $this->shopId)->willReturn($modulePath1);
        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId2, $this->shopId)->willReturn($modulePath2);
        $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId3, $this->shopId)->willReturn($modulePath3);

        $this->activeModulesDataProvider->getModuleIds()->willReturn([$moduleId1, $moduleId2, $moduleId3]);

        $this->filesystem->exists($moduleTemplateDirectory1)->willReturn(false);
        $this->filesystem->exists($moduleTemplateDirectory2)->willReturn(true);
        $this->filesystem->exists($moduleTemplateDirectory3)->willReturn(false);

        $directories = $this->getDirectoryResolver()->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals($moduleTemplateDirectory2, $directories[0]->getDirectory());
    }

    private function prepareMocks(): void
    {
        $this->modulePathResolver = $this->prophesize(ModulePathResolverInterface::class);
        $this->context = $this->prophesize(ContextInterface::class);
        $this->activeModulesDataProvider = $this->prophesize(ActiveModulesDataProviderInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);

        $this->shopId = 1;
        $this->context->getDefaultShopId()->willReturn($this->shopId);
    }

    private function getDirectoryResolver(): ModulesTemplateDirectoryResolver
    {
        return new ModulesTemplateDirectoryResolver(
            $this->activeModulesDataProvider->reveal(),
            $this->modulePathResolver->reveal(),
            $this->context->reveal(),
            $this->filesystem->reveal(),
        );
    }
}
