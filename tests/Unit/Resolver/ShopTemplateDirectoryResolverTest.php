<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    use ProphecyTrait;

    private ShopTemplateDirectoryResolver $shopTemplateDirectoryResolver;
    private Config|ObjectProphecy $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this->prophesize(Config::class);
        $this->shopTemplateDirectoryResolver = new ShopTemplateDirectoryResolver(
            $this->config->reveal(),
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
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getConfigParam('sCustomTheme')->willReturn($childTheme);
        $this->config->getConfigParam('sTheme')->willReturn($parentTheme);
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
        $childTheme = 'child-theme';
        $parentTheme = 'parent-theme';
        $childThemeDir = 'child/theme/dir';
        $parentThemeDir = 'parent/theme/dir';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getConfigParam('sCustomTheme')->willReturn($childTheme);
        $this->config->getConfigParam('sTheme')->willReturn($parentTheme);
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
