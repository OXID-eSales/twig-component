<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChain;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Filesystem;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    use ProphecyTrait;

    private ShopTemplateDirectoryResolver $shopTemplateDirectoryResolver;
    private Config|ObjectProphecy $config;
    private ThemeStateServiceInterface|ObjectProphecy $themeStateService;
    private ThemePathResolverInterface|ObjectProphecy $themePathResolver;
    private Filesystem|ObjectProphecy $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->prophesize(Config::class);
        $this->themeStateService = $this->prophesize(ThemeStateServiceInterface::class);
        $this->themePathResolver = $this->prophesize(ThemePathResolverInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->shopTemplateDirectoryResolver = new ShopTemplateDirectoryResolver(
            $this->config->reveal(),
            $this->themeStateService->reveal(),
            $this->themePathResolver->reveal(),
            $this->filesystem->reveal(),
        );
    }

    public function testGetTemplateDirectoriesWithMissingDirectory(): void
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

    public function testGetTemplateDirectoriesWithThemeInheritanceAndMissingDirectories(): void
    {
        $shopId = 1;
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemePath = 'child/theme/path';
        $parentThemePath = 'parent/theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme(new ThemeChain([$childTheme, $parentTheme])));
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, $shopId)->willReturn($childThemePath);
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, $shopId)->willReturn($parentThemePath);
        $this->filesystem->exists("$childThemePath/tpl")->willReturn(false);
        $this->filesystem->exists("$parentThemePath/tpl")->willReturn(false);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWithThemeInheritanceWhenChildThemeHasNoOwnTemplateDirectory(): void
    {
        $shopId = 1;
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemePath = 'child/theme/path';
        $parentThemePath = 'parent/theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme(new ThemeChain([$childTheme, $parentTheme])));
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, $shopId)->willReturn($childThemePath);
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, $shopId)->willReturn($parentThemePath);
        $this->filesystem->exists("$childThemePath/tpl")->willReturn(false);
        $this->filesystem->exists("$parentThemePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals("$parentThemePath/tpl", $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesWithThemeInheritance(): void
    {
        $shopId = 1;
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemePath = 'child/theme/path';
        $parentThemePath = 'parent/theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme(new ThemeChain([$childTheme, $parentTheme])));
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, $shopId)->willReturn($childThemePath);
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, $shopId)->willReturn($parentThemePath);
        $this->filesystem->exists("$childThemePath/tpl")->willReturn(true);
        $this->filesystem->exists("$parentThemePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertEquals("$childThemePath/tpl", $directories[0]->getDirectory());
        $this->assertEquals("$parentThemePath/tpl", $directories[1]->getDirectory());
    }

    public function testGetTemplateDirectoriesWithoutParentTheme(): void
    {
        $shopId = 1;
        $themeId = 'theme';
        $themePath = 'theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme(new ThemeChain([$themeId])));
        $this->themePathResolver->getFullThemePathFromConfiguration($themeId, $shopId)->willReturn($themePath);
        $this->filesystem->exists("$themePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals("$themePath/tpl", $directories[0]->getDirectory());
    }
}