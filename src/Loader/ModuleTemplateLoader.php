<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use Twig\Loader\FilesystemLoader as TwigLoader;

class ModuleTemplateLoader extends TwigLoader
{
    public function __construct(
        private ModulesTemplateDirectoryResolverInterface $modulesTemplateDirectoryResolver,
        private ActiveModulesDataProviderInterface $activeModulesDataProvider
    ) {
        parent::__construct();
        $this->registerModuleTemplateDirectories();
    }

    private function registerModuleTemplateDirectories(): void
    {
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            $this->prependPath(
                $this->modulesTemplateDirectoryResolver->getAbsolutePath($moduleId),
                $moduleId
            );
        }
    }
}
