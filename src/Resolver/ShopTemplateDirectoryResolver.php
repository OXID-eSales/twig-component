<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemeOverrideDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
        private ThemeStateServiceInterface $themeStateService,
        private ThemeOverrideDirectoryResolverInterface $themeOverrideDirectoryResolver,
        private ThemePathResolverInterface $themePathResolver,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->getShopViewsTemplateDirectories() as $shopTemplateDirectory) {
            $directories[] = new NamespacedDirectory(
                FilesystemLoader::MAIN_NAMESPACE,
                $shopTemplateDirectory
            );
        }

        return $directories;
    }

    private function getShopViewsTemplateDirectories(): array
    {
        if ($this->config->isAdmin()) {
            return $this->addDirectory([], $this->getTemplateDirectoryForAdminTheme());
        }

        if (!($activeTheme = $this->getActiveTheme())) {
            return [];
        }

        $inheritance = $activeTheme->getInheritance();
        $shopId = $this->config->getShopId();

        $shopTemplateDirectories = $this->getTemplateDirectoriesForTheme($inheritance->getThemeId(), $shopId);

        if ($inheritance->hasParentTheme()) {
            $shopTemplateDirectories = array_merge(
                $shopTemplateDirectories,
                $this->getTemplateDirectoriesForTheme($inheritance->getParentThemeId(), $shopId)
            );
        }

        return $shopTemplateDirectories;
    }

    /** @return string[] */
    private function getTemplateDirectoriesForTheme(string $themeId, int $shopId): array
    {
        $directories = $this->themeOverrideDirectoryResolver->resolve($themeId, $shopId);

        return $this->addDirectory($directories, $this->getTemplateDirectoryForTheme($themeId, $shopId));
    }

    /** @param string[] $directories */
    private function addDirectory(array $directories, string $directory): array
    {
        if ($directory) {
            $directories[] = $directory;
        }
        return $directories;
    }

    private function getTemplateDirectoryForAdminTheme(): string
    {
        return (string)$this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            true,
        );
    }

    private function getTemplateDirectoryForTheme(string $themeId, int $shopId): string
    {
        try {
            $themePath = $this->themePathResolver->getFullThemePathFromConfiguration($themeId, $shopId);
        } catch (ThemeConfigurationNotFoundException) {
            return '';
        }

        $templateDirectory = Path::join($themePath, self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME);

        return $this->filesystem->exists($templateDirectory) ? $templateDirectory : '';
    }

    private function getActiveTheme(): ?ActiveTheme
    {
        try {
            return $this->themeStateService->getActiveTheme($this->config->getShopId());
        } catch (ActiveThemeNotFoundException) {
            return null;
        }
    }
}
