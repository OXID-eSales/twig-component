<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
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
            $shopTemplateDirectories = $this->addDirectory(
                $shopTemplateDirectories,
                $this->getTemplateDirectoryForChildTheme()
            );
            $shopTemplateDirectories = $this->addDirectory(
                $shopTemplateDirectories,
                $this->getTemplateDirectoryForParentTheme()
            );
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

    private function getTemplateDirectoryForChildTheme(): string
    {
        return (string)$this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            false,
            null,
            null,
            $this->config->getConfigParam('sCustomTheme')
        );
    }

    private function getTemplateDirectoryForParentTheme(): string
    {
        return (string)$this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            false,
            null,
            null,
            $this->config->getConfigParam('sTheme'),
            true,
            true
        );
    }
}
