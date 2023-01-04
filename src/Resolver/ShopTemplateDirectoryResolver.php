<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
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
            if ($shopTemplateDirectory && $this->filesystem->exists($shopTemplateDirectory)) {
                $directories[] = new NamespacedDirectory(
                    FilesystemLoader::MAIN_NAMESPACE,
                    $shopTemplateDirectory
                );
            }
        }

        return $directories;
    }

    public function getShopViewsTemplateDirectories(): array
    {
        $shopTemplateDirectories = [];
        $directoryForChildTheme = $this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            $this->config->isAdmin(),
            null,
            null,
            $this->config->getConfigParam('sCustomTheme')
        );
        $directoryForParentTheme = $this->config->getDir(
            null,
            self::SHOP_VIEWS_TEMPLATES_DIRECTORY_NAME,
            $this->config->isAdmin(),
            null,
            null,
            $this->config->getConfigParam('sTheme'),
            true,
            true
        );
        if ($directoryForChildTheme) {
            $shopTemplateDirectories[] = $directoryForChildTheme;
        }
        if ($directoryForParentTheme) {
            $shopTemplateDirectories[] = $directoryForParentTheme;
        }

        return $shopTemplateDirectories;
    }
}
