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

class ShopTemplateChainAppender implements ChainAppenderInterface
{
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
        foreach ($this->getThemeIdsOrderedByLoadPriority() as $theme) {
            $countBefore = $templateChain->count();
            $extension = $this->getExtension($templateType, $directory, $theme);
            $templateChain = $this->chainAppender->addToChain($templateChain, $extension, $directory);
            if ($this->isTemplateAddedToTheChain($templateChain, $countBefore)) {
                break;
            }
        }
        return $templateChain;
    }

    private function getThemeIdsOrderedByLoadPriority(): array
    {
        return [
            $this->getExtensionFolderName(),
            $this->getFallbackThemeId(),
        ];
    }

    private function getExtensionFolderName(): string
    {
        return $this->context->isAdmin() ? $this->adminUiName : $this->twigContext->getActiveThemeId();
    }

    private function getFallbackThemeId(): string
    {
        return 'default';
    }

    private function getExtension(
        TemplateTypeInterface $template,
        NamespacedDirectory $directory,
        string $theme
    ): ShopExtensionTemplateType {
        return new ShopExtensionTemplateType(
            $template->getName(),
            $directory->getNamespace(),
            $theme
        );
    }

    private function isTemplateAddedToTheChain(TemplateChain $templateChain, int $countBefore): bool
    {
        return $templateChain->count() > $countBefore;
    }
}
