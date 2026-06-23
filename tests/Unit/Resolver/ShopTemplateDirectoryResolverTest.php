<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    use ProphecyTrait;

    private ShopTemplateDirectoryResolver $shopTemplateDirectoryResolver;
    private Config|ObjectProphecy $config;
    private ActiveThemeServiceInterface|ObjectProphecy $activeThemeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->prophesize(Config::class);
        $this->activeThemeService = $this->prophesize(ActiveThemeServiceInterface::class);
        $this->shopTemplateDirectoryResolver = new ShopTemplateDirectoryResolver(
            $this->config->reveal(),
            $this->activeThemeService->reveal(),
        );
    }

    public function testGetTemplateDirectoriesWithMissingAdminDirectory(): void
    {
        $this->config->isAdmin()->willReturn(true);
        $this->config->getDir(
            null,
            'tpl',
            true,
        )
            ->willReturn(false);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWithAdminTheme(): void
    {
        $adminThemeDir = 'admin/theme/dir';
        $this->config->isAdmin()->willReturn(true);

        $this->config->getDir(
            null,
            'tpl',
            true,
        )
            ->willReturn($adminThemeDir);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals($adminThemeDir, $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesWithoutActiveTheme(): void
    {
        $this->config->isAdmin()->willReturn(false);
        $this->activeThemeService->getActiveThemeSourcePaths()->willReturn([]);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesForSingleActiveTheme(): void
    {
        $this->config->isAdmin()->willReturn(false);
        $this->activeThemeService->getActiveThemeSourcePaths()->willReturn([
            'apex' => '/var/www/vendor/oxid-esales/apex-theme',
        ]);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals('/var/www/vendor/oxid-esales/apex-theme/tpl', $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesForThemeInheritanceResolvesChildBeforeParent(): void
    {
        $this->config->isAdmin()->willReturn(false);
        $this->activeThemeService->getActiveThemeSourcePaths()->willReturn([
            'parent-theme' => '/parent/source',
            'child-theme' => '/child/source',
        ]);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertEquals('/child/source/tpl', $directories[0]->getDirectory());
        $this->assertEquals('/parent/source/tpl', $directories[1]->getDirectory());
    }
}
