<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ModuleExtensions;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ChainAppenderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\TwigContextInterface;

readonly class ShopTemplateChainAppender implements ChainAppenderInterface
{
    private const FALLBACK_EXTENSION_FOLDER_NAME = 'default';

    public function __construct(
        private TwigContextInterface $twigContext,
        private ChainAppenderInterface $chainAppender,
        private ContextInterface $context,
        private string $adminUiName,
    ) {
    }

    public function addToChain(
        TemplateChain $templateChain,
        TemplateTypeInterface $templateType,
        NamespacedDirectory $directory
    ): TemplateChain {
        foreach ($this->getExtensionFolderNamesOrderedByLoadPriority() as $extensionFolderName) {
            $countBefore = $templateChain->count();
            $extension = $this->getExtension($templateType, $directory, $extensionFolderName);
            $templateChain = $this->chainAppender->addToChain($templateChain, $extension, $directory);
            if ($this->isTemplateAddedToTheChain($templateChain, $countBefore)) {
                break;
            }
        }
        return $templateChain;
    }

    /** @return string[] */
    private function getExtensionFolderNamesOrderedByLoadPriority(): array
    {
        return [
            $this->getExtensionFolderName(),
            self::FALLBACK_EXTENSION_FOLDER_NAME,
        ];
    }

    private function getExtensionFolderName(): string
    {
        if ($this->context->isAdmin()) {
            return $this->adminUiName;
        }

        return $this->twigContext->getActiveThemeId();
    }

    private function getExtension(
        TemplateTypeInterface $template,
        NamespacedDirectory $directory,
        string $extensionFolderName
    ): ShopExtensionTemplateType {
        return new ShopExtensionTemplateType(
            $template->getName(),
            $directory->getNamespace(),
            $extensionFolderName
        );
    }

    private function isTemplateAddedToTheChain(TemplateChain $templateChain, int $countBefore): bool
    {
        return $templateChain->count() > $countBefore;
    }
}
