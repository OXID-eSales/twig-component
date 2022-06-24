<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleTemplateExtensionChain;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Psr\Log\LoggerInterface;

class TemplateChainSorter implements TemplateChainSorterInterface
{
    public function __construct(
        private SortingConfigurationValidatorInterface $sortingConfigurationValidator,
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ContextInterface $context,
        private LoggerInterface $logger,
    ) {
    }

    public function sort(TemplateChain $unsortedChain, TemplateTypeInterface $extendedTemplate): TemplateChain
    {
        $sortingConfiguration = $this->shopConfigurationDao
            ->get($this->context->getCurrentShopId())
            ->getModuleTemplateExtensionChain();

        return $this->getSortedChain($unsortedChain, $sortingConfiguration, $extendedTemplate);
    }

    private function getSortedChain(
        TemplateChain $unsortedChain,
        ModuleTemplateExtensionChain $sortingConfiguration,
        TemplateTypeInterface $extendedTemplate
    ): TemplateChain {
        $sortedChain = new TemplateChain();
        $templateName = $this->getTemplateAliasInShopConfiguration($extendedTemplate);
        foreach ($sortingConfiguration->getTemplateLoadingPriority($templateName) as $moduleId) {
            try {
                $this->sortingConfigurationValidator->validateModuleId($moduleId, $unsortedChain, $extendedTemplate);
                $template = $unsortedChain->getByModuleId($moduleId);
                $sortedChain->append($template);
                $unsortedChain->remove($template);
            } catch (InvalidSortingConfigurationException $e) {
                $this->logInvalidSortingConfiguration($templateName, $e);
            }
        }
        if ($unsortedChain->count()) {
            $sortedChain->appendChain($unsortedChain);
        }

        return $sortedChain;
    }

    private function getTemplateAliasInShopConfiguration(TemplateTypeInterface $extendedTemplate): string
    {
        return $extendedTemplate->isShopTemplate()
            ? $extendedTemplate->getRelativeFilePath()
            : $extendedTemplate->getFullyQualifiedName();
    }

    private function logInvalidSortingConfiguration(string $templateName, InvalidSortingConfigurationException $e): void
    {
        $this->logger->error(
            "Incomplete sorting of template chain for '$templateName' has occurred! "
            . "The error was: \"{$e->getMessage()}\" "
            . 'Please check the correctness of `templateExtensions` in your ShopConfiguration!'
        );
    }
}
