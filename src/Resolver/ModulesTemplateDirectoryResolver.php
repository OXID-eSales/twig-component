<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ModulesTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    public function __construct(
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->activeModulesDataProvider->getModulePaths() as $moduleId => $modulePath) {
            $moduleTemplateDirectory = Path::join($modulePath, 'views', 'twig');
            if ($this->filesystem->exists($moduleTemplateDirectory)) {
                $directories[] = new NamespacedDirectory(
                    $moduleId,
                    $moduleTemplateDirectory
                );
            }
        }

        return $directories;
    }
}
