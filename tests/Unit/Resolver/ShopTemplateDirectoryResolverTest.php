<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const CHILD_THEME_ID = 'childTheme';
    private const PARENT_THEME_ID = 'parentTheme';
    private const PARENT_THEME_DIRECTORY = 'views/parentTheme/tpl';

    public function testGetTemplateDirectoriesForAdminReturnsAdminThemeDirectory(): void
    {
        $adminThemeDirectory = 'admin/theme/tpl';
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createAdminConfig($adminThemeDirectory),
            $this->createStub(ActiveThemeProviderInterface::class)
        );

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame($adminThemeDirectory, $directories[0]->getDirectory());
        $this->assertSame(FilesystemLoader::MAIN_NAMESPACE, $directories[0]->getNamespace());
    }

    public function testGetTemplateDirectoriesForAdminSkipsMissingDirectory(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createAdminConfig(false),
            $this->createStub(ActiveThemeProviderInterface::class)
        );

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    public function testGetTemplateDirectoriesWithoutActiveThemeReturnsNothing(): void
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveTheme')->willThrowException(new ActiveThemeNotFoundException());
        $resolver = new ShopTemplateDirectoryResolver($this->createFrontendConfig(), $activeThemeProvider);

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    public function testGetTemplateDirectoriesForThemeWithoutParentReturnsOnlyActiveThemeDirectory(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig([self::PARENT_THEME_ID => self::PARENT_THEME_DIRECTORY]),
            $this->createActiveThemeProvider(new ActiveTheme(self::PARENT_THEME_ID))
        );

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame(self::PARENT_THEME_DIRECTORY, $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesForChildThemeListsChildBeforeParent(): void
    {
        $childThemeDirectory = 'views/childTheme/tpl';
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig([
                self::CHILD_THEME_ID => $childThemeDirectory,
                self::PARENT_THEME_ID => self::PARENT_THEME_DIRECTORY,
            ]),
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertSame($childThemeDirectory, $directories[0]->getDirectory());
        $this->assertSame(self::PARENT_THEME_DIRECTORY, $directories[1]->getDirectory());
    }

    public function testGetTemplateDirectoriesForChildThemeWithoutOwnTemplatesReturnsParentDirectory(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig([self::PARENT_THEME_ID => self::PARENT_THEME_DIRECTORY]),
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame(self::PARENT_THEME_DIRECTORY, $directories[0]->getDirectory());
    }

    public function testGetTemplateDirectoriesForChildThemeWithoutAnyTemplatesReturnsNothing(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig(),
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    public function testGetTemplateDirectoriesResolvesEachThemeDirectoryWithoutParentFallback(): void
    {
        $requestedThemes = [];
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn(false);
        $config->method('getShopId')->willReturn(self::SHOP_ID);
        $config->method('getDir')->willReturnCallback(
            function ($file, $dir, $admin, $lang, $shop, $theme, $absolute, $ignoreParent) use (&$requestedThemes) {
                $requestedThemes[$theme] = $ignoreParent;
                return false;
            }
        );
        $resolver = new ShopTemplateDirectoryResolver(
            $config,
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $resolver->getTemplateDirectories();

        $this->assertSame([self::CHILD_THEME_ID => true, self::PARENT_THEME_ID => true], $requestedThemes);
    }

    public function testGetTemplateDirectoriesResolvesActiveThemeForCurrentShop(): void
    {
        $activeThemeProvider = $this->createMock(ActiveThemeProviderInterface::class);
        $activeThemeProvider
            ->expects($this->once())
            ->method('getActiveTheme')
            ->with(self::SHOP_ID)
            ->willReturn(new ActiveTheme(self::PARENT_THEME_ID));
        $resolver = new ShopTemplateDirectoryResolver($this->createFrontendConfig(), $activeThemeProvider);

        $resolver->getTemplateDirectories();
    }

    private function createAdminConfig(string|false $adminThemeDirectory): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn(true);
        $config->method('getDir')->willReturn($adminThemeDirectory);

        return $config;
    }

    /** @param string[] $directoriesByThemeId */
    private function createFrontendConfig(array $directoriesByThemeId = []): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn(false);
        $config->method('getShopId')->willReturn(self::SHOP_ID);
        $config->method('getDir')->willReturnCallback(
            fn ($file, $dir, $admin, $lang, $shop, $theme) => $directoriesByThemeId[$theme] ?? false
        );

        return $config;
    }

    private function createActiveThemeProvider(ActiveTheme $activeTheme): ActiveThemeProviderInterface
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveTheme')->willReturn($activeTheme);

        return $activeThemeProvider;
    }
}
