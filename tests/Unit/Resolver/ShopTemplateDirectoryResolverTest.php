<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\DataObject\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

final class ShopTemplateDirectoryResolverTest extends TestCase
{
    private const CHILD_THEME_ID = 'childTheme';
    private const PARENT_THEME_ID = 'parentTheme';

    public function testReturnsAdminThemeDirectoryInAdmin(): void
    {
        $config = $this->createConfig(isAdmin: true);
        $config->method('getDir')->willReturn('admin/tpl');
        $resolver = new ShopTemplateDirectoryResolver($config, $this->createStub(ActiveThemeProviderInterface::class));

        $this->assertEquals(
            [new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, 'admin/tpl')],
            $resolver->getTemplateDirectories()
        );
    }

    public function testReturnsChildThemeDirectoryBeforeParentThemeDirectory(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig([
                self::CHILD_THEME_ID => 'child/tpl',
                self::PARENT_THEME_ID => 'parent/tpl',
            ]),
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $this->assertEquals(
            [
                new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, 'child/tpl'),
                new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, 'parent/tpl'),
            ],
            $resolver->getTemplateDirectories()
        );
    }

    public function testSkipsThemesWithoutTemplateDirectory(): void
    {
        $resolver = new ShopTemplateDirectoryResolver(
            $this->createFrontendConfig([self::PARENT_THEME_ID => 'parent/tpl']),
            $this->createActiveThemeProvider(new ActiveTheme(self::CHILD_THEME_ID, self::PARENT_THEME_ID))
        );

        $this->assertEquals(
            [new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, 'parent/tpl')],
            $resolver->getTemplateDirectories()
        );
    }

    public function testReturnsNothingWithoutActiveTheme(): void
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveTheme')->willThrowException(new ActiveThemeNotFoundException());
        $resolver = new ShopTemplateDirectoryResolver($this->createFrontendConfig([]), $activeThemeProvider);

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    private function createConfig(bool $isAdmin): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn($isAdmin);

        return $config;
    }

    /** @param array<string, string> $templateDirectoriesByThemeId */
    private function createFrontendConfig(array $templateDirectoriesByThemeId): Config
    {
        $config = $this->createConfig(isAdmin: false);
        $config->method('getDir')->willReturnCallback(
            fn ($file, $dir, $admin, $lang, $shop, $themeId) => $templateDirectoriesByThemeId[$themeId] ?? false
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
