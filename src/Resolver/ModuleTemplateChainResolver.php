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

    public function __construct(
        ActiveModulesDataProviderInterface $activeModulesDataProvider,
        ModuleTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolver
    ) {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->moduleTemplateDirectoryResolver = $moduleTemplateDirectoryResolver;
    }

    /** @inheritDoc */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            if (file_exists($this->getAbsoluteTemplatePath($moduleId, $templateName))) {
                $templateChain[] = "@$moduleId/$templateName";
            }
        }
        return $templateChain;
    }

    private function getAbsoluteTemplatePath(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $templateName
        );
    }
}
