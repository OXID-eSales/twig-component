<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
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
        $shopTemplateDirectories = [];
        if ($this->config->isAdmin()) {
            $shopTemplateDirectories = $this->addDirectory(
                $shopTemplateDirectories,
                $this->getTemplateDirectoryForAdminTheme()
            );
        } else {
            foreach ($this->getActiveThemeChain() as $themeId) {
                $shopTemplateDirectories = $this->addDirectory(
                    $shopTemplateDirectories,
                    $this->getTemplateDirectoryForTheme($themeId)
                );
            }
        }

        return $shopTemplateDirectories;
    }

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

    private function getTemplateDirectoryForTheme(string $themeId): string
    {
        try {
            $themePath = $this->themePathResolver->getFullThemePathFromConfiguration(
                $themeId,
                $this->config->getShopId()
            );
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

    /** @return string[] */
    private function getActiveThemeChain(): array
    {
        return $this->getActiveTheme()?->getChain()->getThemeIds() ?? [];
    }
}
