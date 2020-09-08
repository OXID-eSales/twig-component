<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateDirectoryResolverInterface;
use Twig\Loader\FilesystemLoader as TwigLoader;

class ModuleTemplateLoader extends TwigLoader
{
    /**
     * @var ModuleTemplateChainResolverInterface
     */
    private $moduleTemplateChainResolver;

    /**
     * @var ModuleTemplateDirectoryResolverInterface
     */
    private $moduleTemplateDirectoryResolver;

    /**
     * @var TemplateNameResolverInterface
     */
    private $templateNameResolver;

    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    public function __construct(
        ModuleTemplateChainResolverInterface $moduleTemplateChainResolver,
        ModuleTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolverInterface,
        TemplateNameResolverInterface $templateNameResolver,
        ActiveModulesDataProviderInterface $activeModulesDataProvider
    ) {
        parent::__construct();
        $this->moduleTemplateChainResolver = $moduleTemplateChainResolver;
        $this->moduleTemplateDirectoryResolver = $moduleTemplateDirectoryResolverInterface;
        $this->templateNameResolver = $templateNameResolver;
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->registerModuleTemplateDirectories();
    }

    public function findTemplate($name, $throw = true)
    {
        $templateName = $this->templateNameResolver->resolve($name);

        if ($this->isModuleTemplate($templateName)) {
            return parent::findTemplate($templateName, $throw);
        }

        if (!$this->isModuleTemplate($templateName) && $this->hasModuleParentTemplates($templateName)) {
            return parent::findTemplate($this->getFirstModuleParentTemplate($templateName), $throw);
        }
    }

    private function registerModuleTemplateDirectories(): void
    {
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            $moduleTemplateDirectory = $this->moduleTemplateDirectoryResolver
                ->getAbsolutePath($moduleId);

            if (is_dir($moduleTemplateDirectory)) {
                $this->prependPath($moduleTemplateDirectory, $moduleId);
            }
        }
    }

    private function isModuleTemplate(string $name): bool
    {
        return parent::findTemplate($name, false) !== false;
    }

    private function hasModuleParentTemplates(string $name): bool
    {
        return !empty($this->moduleTemplateChainResolver->getChain($name));
    }

    private function getFirstModuleParentTemplate(string $name): string
    {
        return $this->moduleTemplateChainResolver->getChain($name)[0];
    }
}
