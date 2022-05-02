<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Loader\FilesystemLoader;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    public function __construct(
        private UtilsView $utilsView,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->utilsView->getTemplateDirs() as $shopTemplateDirectory) {
            if ($this->filesystem->exists($shopTemplateDirectory)) {
                $directories[] = new NamespacedDirectory(
                    FilesystemLoader::MAIN_NAMESPACE,
                    $shopTemplateDirectory
                );
            }
        }

        return $directories;
    }
}
