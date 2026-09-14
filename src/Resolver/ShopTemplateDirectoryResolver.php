<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Twig\Loader\FilesystemLoader;

readonly class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
        private ActiveThemeProviderInterface $activeThemeProvider,
    ) {
    }

    public function getTemplateDirectories(): array
    {
        return array_map(
            static fn (string $directory) => new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, $directory),
            array_values(array_filter($this->getShopViewsTemplateDirectories()))
        );
    }

    /** @return string[] */
    private function getShopViewsTemplateDirectories(): array
    {
        if ($this->config->isAdmin()) {
            return [$this->getTemplateDirectoryForAdminTheme()];
        }

        return array_map($this->getTemplateDirectoryForTheme(...), $this->getActiveThemeIds());
    }

    /** @return string[] */
    private function getActiveThemeIds(): array
    {
        try {
            $activeTheme = $this->activeThemeProvider->getActiveTheme();
        } catch (ActiveThemeNotFoundException) {
            return [];
        }

        return $activeTheme->hasParentTheme()
            ? [$activeTheme->getId(), $activeTheme->getParentThemeId()]
            : [$activeTheme->getId()];
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
