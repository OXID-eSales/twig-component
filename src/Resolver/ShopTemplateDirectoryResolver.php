<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Path;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
        private ActiveThemeServiceInterface $activeThemeService,
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

        return $this->getTemplateDirectoriesForActiveTheme();
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

    /**
     * @return string[]
     */
    private function getTemplateDirectoriesForActiveTheme(): array
    {
        $directories = [];
        foreach (array_reverse($this->activeThemeService->getActiveThemeSourcePaths()) as $themeSourcePath) {
            $directories[] = Path::join($themeSourcePath, self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME);
        }

        return $directories;
    }
}
