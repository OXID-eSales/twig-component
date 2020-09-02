<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateDirectoryResolverInterface;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader as TwigLoader;

class ModuleTemplateLoader extends TwigLoader
{
    /**
     * @var ModuleTemplateChainResolverInterface
     */
    private $moduleTemplateChainResolver;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleTemplateDirectoryResolverInterface
     */
    private $moduleTemplateDirectoryResolver;

    /**
     * @var TemplateNameResolverInterface
     */
    private $templateNameResolver;

    public function __construct(
        ModuleTemplateChainResolverInterface $moduleTemplateChainResolver,
        ContextInterface $context,
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolverInterface,
        TemplateNameResolverInterface $templateNameResolver
    ) {
        parent::__construct();
        $this->moduleTemplateChainResolver = $moduleTemplateChainResolver;
        $this->context = $context;
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleTemplateDirectoryResolver = $moduleTemplateDirectoryResolverInterface;
        $this->templateNameResolver = $templateNameResolver;
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
        foreach ($this->getModuleConfigurations() as $moduleConfiguration) {
            $moduleTemplateDirectory = $this
                ->moduleTemplateDirectoryResolver
                ->getAbsolutePath($moduleConfiguration->getId());

            if (is_dir($moduleTemplateDirectory)) {
                $this->prependPath($moduleTemplateDirectory, $moduleConfiguration->getId());
            }
        }
    }

    /**
     * @return ModuleConfiguration[]
     */
    private function getModuleConfigurations(): array
    {
        return $this
            ->shopConfigurationDao
            ->get($this->context->getCurrentShopId())
            ->getModuleConfigurations();
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
