<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;

class FilesystemLoader extends \Twig\Loader\FilesystemLoader
{
    public function __construct(
        private TemplateDirectoryResolverInterface $templateDirectoryResolver,
        $paths = [],
        string $rootPath = null
    ) {
        parent::__construct(
            $paths,
            $rootPath
        );

        $this->setTemplatePaths();
    }

    private function setTemplatePaths(): void
    {
        foreach ($this->templateDirectoryResolver->getTemplateDirectories() as $namespacedDirectory) {
            $this->addPath(
                $namespacedDirectory->getDirectory(),
                $namespacedDirectory->getNamespace()
            );
        }
    }
}
