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
use Psr\Log\LoggerInterface;

class TemplateChainSorter implements TemplateChainSorterInterface
{
    public function __construct(
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ContextInterface $context,
        private LoggerInterface $logger,
    ) {
    }

    public function sort(TemplateChain $unsortedChain, TemplateTypeInterface $extendedTemplate): TemplateChain
    {
        $templateName = $this->getTemplateAliasInShopConfiguration($extendedTemplate);
        $templateLoadingPriority = $this->shopConfigurationDao
            ->get($this->context->getCurrentShopId())
            ->getModuleTemplateExtensionChain()
            ->getTemplateLoadingPriority($templateName);

        if ($this->isConfigurationEmpty($templateLoadingPriority)) {
            return $unsortedChain;
        }

        try {
            $sortedChain = $this->getSortedChain($unsortedChain, $templateLoadingPriority);
        } catch (TemplateForModuleIdNotInChainException $e) {
            $this->logTemplateChainMisconfiguration($templateName, $e);
            $sortedChain = $unsortedChain;
        }

        return $sortedChain;
    }

    private function getTemplateAliasInShopConfiguration(TemplateTypeInterface $extendedTemplate): string
    {
        return $extendedTemplate->isShopTemplate()
            ? $extendedTemplate->getRelativeFilePath()
            : $extendedTemplate->getFullyQualifiedName();
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

    private function isConfigurationEmpty(ModuleIdChain $templateLoadingPriority): bool
    {
        return !$templateLoadingPriority->getIterator()->count();
    }

    private function logTemplateChainMisconfiguration(string $templateName, TemplateForModuleIdNotInChainException $e): void
    {
        $this->logger->error(
            "Template chain for '$templateName' was not sorted: $e!'
                . Please make sure Shop Configuration value of `templateExtensions` is correct."
        );
    }
}
