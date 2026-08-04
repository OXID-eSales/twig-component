<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemeOverrideDirectoryResolverInterface;
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

    private const SHOP_ID = 1;

    private ShopTemplateDirectoryResolver $shopTemplateDirectoryResolver;
    private Config|ObjectProphecy $config;
    private ThemeStateServiceInterface|ObjectProphecy $themeStateService;
    private ThemeOverrideDirectoryResolverInterface|ObjectProphecy $themeOverrideDirectoryResolver;
    private ThemePathResolverInterface|ObjectProphecy $themePathResolver;
    private Filesystem|ObjectProphecy $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->prophesize(Config::class);
        $this->themeStateService = $this->prophesize(ThemeStateServiceInterface::class);
        $this->themeOverrideDirectoryResolver = $this->prophesize(ThemeOverrideDirectoryResolverInterface::class);
        $this->themePathResolver = $this->prophesize(ThemePathResolverInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->shopTemplateDirectoryResolver = new ShopTemplateDirectoryResolver(
            $this->config->reveal(),
            $this->themeStateService->reveal(),
            $this->themeOverrideDirectoryResolver->reveal(),
            $this->themePathResolver->reveal(),
            $this->filesystem->reveal(),
        );

        $this->config->getShopId()->willReturn(self::SHOP_ID);
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
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $this->config->isAdmin()->willReturn(false);
        $this->themeStateService->getActiveTheme(self::SHOP_ID)->willReturn(new ActiveTheme(new ThemeInheritance($childTheme, $parentTheme)));
        $this->themeOverrideDirectoryResolver->resolve($childTheme, self::SHOP_ID)->willReturn([]);
        $this->themeOverrideDirectoryResolver->resolve($parentTheme, self::SHOP_ID)->willReturn([]);
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, self::SHOP_ID)->willReturn('child/theme/path');
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, self::SHOP_ID)->willReturn('parent/theme/path');
        $this->filesystem->exists('child/theme/path/tpl')->willReturn(false);
        $this->filesystem->exists('parent/theme/path/tpl')->willReturn(false);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWithThemeInheritanceWhenChildThemeHasNoOwnTemplateDirectory(): void
    {
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $parentThemePath = 'parent/theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->themeStateService->getActiveTheme(self::SHOP_ID)->willReturn(new ActiveTheme(new ThemeInheritance($childTheme, $parentTheme)));
        $this->themeOverrideDirectoryResolver->resolve($childTheme, self::SHOP_ID)->willReturn([]);
        $this->themeOverrideDirectoryResolver->resolve($parentTheme, self::SHOP_ID)->willReturn([]);
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, self::SHOP_ID)->willReturn('child/theme/path');
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, self::SHOP_ID)->willReturn($parentThemePath);
        $this->filesystem->exists('child/theme/path/tpl')->willReturn(false);
        $this->filesystem->exists("$parentThemePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals("$parentThemePath/tpl", $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesWithThemeInheritance(): void
    {
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemePath = 'child/theme/path';
        $parentThemePath = 'parent/theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->themeStateService->getActiveTheme(self::SHOP_ID)->willReturn(new ActiveTheme(new ThemeInheritance($childTheme, $parentTheme)));
        $this->themeOverrideDirectoryResolver->resolve($childTheme, self::SHOP_ID)->willReturn([]);
        $this->themeOverrideDirectoryResolver->resolve($parentTheme, self::SHOP_ID)->willReturn([]);
        $this->themePathResolver->getFullThemePathFromConfiguration($childTheme, self::SHOP_ID)->willReturn($childThemePath);
        $this->themePathResolver->getFullThemePathFromConfiguration($parentTheme, self::SHOP_ID)->willReturn($parentThemePath);
        $this->filesystem->exists("$childThemePath/tpl")->willReturn(true);
        $this->filesystem->exists("$parentThemePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertEquals("$childThemePath/tpl", $directories[0]->getDirectory());
        $this->assertEquals("$parentThemePath/tpl", $directories[1]->getDirectory());
    }

    public function testGetTemplateDirectoriesWithoutParentTheme(): void
    {
        $themeId = 'theme';
        $themePath = 'theme/path';
        $this->config->isAdmin()->willReturn(false);
        $this->themeStateService->getActiveTheme(self::SHOP_ID)->willReturn(new ActiveTheme(new ThemeInheritance($themeId, null)));
        $this->themeOverrideDirectoryResolver->resolve($themeId, self::SHOP_ID)->willReturn([]);
        $this->themePathResolver->getFullThemePathFromConfiguration($themeId, self::SHOP_ID)->willReturn($themePath);
        $this->filesystem->exists("$themePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertEquals("$themePath/tpl", $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesIncludesOverrideDirectoriesBeforeThemesOwnDirectory(): void
    {
        $themeId = 'theme';
        $themePath = 'theme/path';
        $overrideDirectory = 'views/theme/1/tpl';
        $this->config->isAdmin()->willReturn(false);
        $this->themeStateService->getActiveTheme(self::SHOP_ID)->willReturn(new ActiveTheme(new ThemeInheritance($themeId, null)));
        $this->themeOverrideDirectoryResolver->resolve($themeId, self::SHOP_ID)->willReturn([$overrideDirectory]);
        $this->themePathResolver->getFullThemePathFromConfiguration($themeId, self::SHOP_ID)->willReturn($themePath);
        $this->filesystem->exists("$themePath/tpl")->willReturn(true);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertEquals($overrideDirectory, $directories[0]->getDirectory());
        $this->assertEquals("$themePath/tpl", $directories[1]->getDirectory());
    }
}
