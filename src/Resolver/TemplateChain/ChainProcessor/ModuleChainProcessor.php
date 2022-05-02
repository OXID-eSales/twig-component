<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Twig\Loader\FilesystemLoader;

class ModuleChainProcessor implements ChainProcessorInterface
{
    public function __construct(
        private ShopTemplateExtensionProcessor $shopTemplateExtensionProcessor,
        private ModuleTemplateProcessor $moduleTemplateProcessor,
        private ModuleTemplateExtensionProcessor $moduleTemplateExtensionProcessor,
    ) {
    }

    public function process(array $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): array
    {
        if ($this->extendingShopTemplate($templateType)) {
            $templateChain = $this->shopTemplateExtensionProcessor->process($templateChain, $templateType, $directory);
        } elseif ($this->extendingSameNamespace($templateType, $directory)) {
            $templateChain = $this->moduleTemplateProcessor->process($templateChain, $templateType, $directory);
        }
        return $this->moduleTemplateExtensionProcessor->process($templateChain, $templateType, $directory);
    }

    private function extendingShopTemplate(TemplateTypeInterface $templateType): bool
    {
        return $templateType->getParentNamespace() === FilesystemLoader::MAIN_NAMESPACE;
    }

    private function extendingSameNamespace(TemplateTypeInterface $templateType, NamespacedDirectory $directory): bool
    {
        return $templateType->getNamespace() === $directory->getNamespace();
    }
}
