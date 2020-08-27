<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\TwigContextInterface;
use Webmozart\PathUtil\Path;

class ModuleTemplateChainResolver implements ModuleTemplateChainResolverInterface
{
    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    /**
     * @var ModuleTemplateDirectoryResolverInterface
     */
    private $moduleTemplateDirectoryResolver;

    /**
     * @var TwigContextInterface
     */
    private $twigContext;

    public function __construct(
        ActiveModulesDataProviderInterface $activeModulesDataProvider,
        ModuleTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolver,
        TwigContextInterface $twigContext
    ) {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->moduleTemplateDirectoryResolver = $moduleTemplateDirectoryResolver;
        $this->twigContext = $twigContext;
    }

    /** @inheritDoc */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            if ($this->moduleHasTemplateForActiveTheme($moduleId, $templateName)) {
                $templateChain[] = "@$moduleId/{$this->twigContext->getActiveThemeId()}/$templateName";
            } elseif (file_exists($this->getAbsoluteTemplatePath($moduleId, $templateName))) {
                $templateChain[] = "@$moduleId/$templateName";
            }
        }
        return $templateChain;
    }

    private function getAbsoluteTemplatePathForTheme(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $this->twigContext->getActiveThemeId(),
            $templateName
        );
    }

    private function getAbsoluteTemplatePath(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $templateName
        );
    }

    private function moduleHasTemplateForActiveTheme(string $moduleId, string $templateName): bool
    {
        return file_exists($this->getAbsoluteTemplatePathForTheme($moduleId, $templateName));
    }
}
