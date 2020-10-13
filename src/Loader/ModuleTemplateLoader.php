<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use Twig\Loader\FilesystemLoader as TwigLoader;

class ModuleTemplateLoader extends TwigLoader
{
    /** @var ModulesTemplateDirectoryResolverInterface */
    private $modulesTemplateDirectoryResolver;
    /** @var TemplateNameResolverInterface */
    private $templateNameResolver;
    /** @var ActiveModulesDataProviderInterface */
    private $activeModulesDataProvider;

    public function __construct(
        ModulesTemplateDirectoryResolverInterface $modulesTemplateDirectoryResolverInterface,
        TemplateNameResolverInterface $templateNameResolver,
        ActiveModulesDataProviderInterface $activeModulesDataProvider
    ) {
        parent::__construct();
        $this->modulesTemplateDirectoryResolver = $modulesTemplateDirectoryResolverInterface;
        $this->templateNameResolver = $templateNameResolver;
        $this->activeModulesDataProvider = $activeModulesDataProvider;

        $this->registerModuleTemplateDirectories();
    }

    /** @inheritDoc */
    public function findTemplate($name, $throw = true)
    {
        return parent::findTemplate(
            $this->templateNameResolver->resolve($name),
            $throw
        );
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
