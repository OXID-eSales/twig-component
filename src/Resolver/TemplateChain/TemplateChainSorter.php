<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleIdChain;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class TemplateChainSorter implements TemplateChainSorterInterface
{
    public function __construct(
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ContextInterface $context,
    ) {
    }

    public function sort(TemplateChain $unsortedChain, TemplateTypeInterface $extendedTemplate): TemplateChain
    {
        $templateName = $this->getTemplateAliasInShopConfiguration($extendedTemplate);
        $shopConfiguration = $this->shopConfigurationDao->get(
            $this->context->getCurrentShopId()
        );
        $moduleTemplateExtensionChain = $shopConfiguration->getModuleTemplateExtensionChain();
        $templateLoadingPriority = $moduleTemplateExtensionChain->getTemplateLoadingPriority($templateName);

        return !$templateLoadingPriority->getIterator()->count()
            ? $unsortedChain
            : $this->getSortedChain($unsortedChain, $templateLoadingPriority);
    }

    private function getSortedChain(TemplateChain $unsortedChain, ModuleIdChain $templateLoadingPriority): TemplateChain
    {
        $sortedChain = new TemplateChain();
        foreach ($templateLoadingPriority as $moduleId) {
            $template = $unsortedChain->getByModuleId($moduleId);
            $sortedChain->append($template);
            $unsortedChain->remove($template);
        }
        if ($unsortedChain->count()) {
            $sortedChain->appendChain($unsortedChain);
        }

        return $sortedChain;
    }

    public function getTemplateAliasInShopConfiguration(TemplateTypeInterface $extendedTemplate): string
    {
        return $extendedTemplate->isShopTemplate()
            ? $extendedTemplate->getRelativeFilePath()
            : $extendedTemplate->getFullyQualifiedName();
    }
}
