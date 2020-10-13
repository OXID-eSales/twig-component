<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use Twig\Loader\FilesystemLoader as TwigLoader;

class ModuleTemplateLoader extends TwigLoader
{
    /** @var ModulesTemplateDirectoryResolverInterface */
    private $modulesTemplateDirectoryResolver;
    /** @var ActiveModulesDataProviderInterface */
    private $activeModulesDataProvider;

    public function __construct(
        ModulesTemplateDirectoryResolverInterface $modulesTemplateDirectoryResolverInterface,
        ActiveModulesDataProviderInterface $activeModulesDataProvider
    ) {
        parent::__construct();

        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->modulesTemplateDirectoryResolver = $modulesTemplateDirectoryResolverInterface;
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
