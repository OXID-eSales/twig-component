<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ModulesTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    public function __construct(
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private ModulePathResolverInterface $modulePathResolver,
        private BasicContextInterface $context,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            $moduleTemplateDirectory = Path::join(
                $this->modulePathResolver->getFullModulePathFromConfiguration(
                    $moduleId,
                    $this->context->getDefaultShopId()
                ),
                'views',
                'twig',
            );
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
