<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    use ProphecyTrait;

    private ShopTemplateDirectoryResolver $shopTemplateDirectoryResolver;
    private Config|ObjectProphecy $config;
    private ThemeStateServiceInterface|ObjectProphecy $themeStateService;
    private ThemeParentProviderInterface|ObjectProphecy $themeParentProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->prophesize(Config::class);
        $this->themeStateService = $this->prophesize(ThemeStateServiceInterface::class);
        $this->themeParentProvider = $this->prophesize(ThemeParentProviderInterface::class);
        $this->shopTemplateDirectoryResolver = new ShopTemplateDirectoryResolver(
            $this->config->reveal(),
            $this->themeStateService->reveal(),
            $this->themeParentProvider->reveal(),
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
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme($childTheme, true));
        $this->themeParentProvider->getParentThemeId($childTheme, $shopId)->willReturn($parentTheme);
        $this->config->getDir(
            null,
            'tpl',
            false,
            null,
            null,
            $childTheme
        )
            ->willReturn(false);

        $this->config->getDir(
            null,
            'tpl',
            false,
            null,
            null,
            $parentTheme,
            true,
            true
        )
            ->willReturn(false);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertEmpty($directories);
    }

    public function testGetTemplateDirectoriesWithThemeInheritance(): void
    {
        $shopId = 1;
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemeDir = 'child/theme/dir';
        $parentThemeDir = 'parent/theme/dir';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveTheme($shopId)->willReturn(new ActiveTheme($childTheme, true));
        $this->themeParentProvider->getParentThemeId($childTheme, $shopId)->willReturn($parentTheme);
        $this->config->getDir(
            null,
            'tpl',
            false,
            null,
            null,
            $childTheme
        )
            ->willReturn($childThemeDir);

        $this->config->getDir(
            null,
            'tpl',
            false,
            null,
            null,
            $parentTheme,
            true,
            true
        )
            ->willReturn($parentThemeDir);

        $directories = $this->shopTemplateDirectoryResolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertEquals($childThemeDir, $directories[0]->getDirectory());
        $this->assertEquals($parentThemeDir, $directories[1]->getDirectory());
    }
}
