<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
        private ActiveThemeProviderInterface $activeThemeProvider,
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

        $shopTemplateDirectories = [];
        foreach ($this->getActiveThemeIds() as $themeId) {
            $shopTemplateDirectories = $this->addDirectory(
                $shopTemplateDirectories,
                $this->getTemplateDirectoryForTheme($themeId)
            );
        }

        return $shopTemplateDirectories;
    }

    /** @return string[] */
    private function getActiveThemeIds(): array
    {
        try {
            $activeTheme = $this->activeThemeProvider->getActiveTheme($this->config->getShopId());
        } catch (ActiveThemeNotFoundException) {
            return [];
        }

        return array_filter([$activeTheme->getId(), $activeTheme->getParentThemeId()]);
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
        return (string)$this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            false,
            null,
            null,
            $themeId,
            true,
            true
        );
    }
}
