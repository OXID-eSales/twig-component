<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ModuleExtensions;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ChainAppenderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\TemplateTypeCheckerInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class ShopExtensionTemplateHandler implements ChainAppenderInterface, TemplateTypeCheckerInterface
{
    public function __construct(
        private ChainAppenderInterface $shopTemplateChainAppender,
    ) {
    }

    public function addToChain(
        TemplateChain $templateChain,
        TemplateTypeInterface $templateType,
        NamespacedDirectory $directory
    ): TemplateChain {
        if (!$this->canHandle($templateType)) {
            return $templateChain;
        }
        return $this->shopTemplateChainAppender->addToChain($templateChain, $templateType, $directory);
    }

    public function canHandle(TemplateTypeInterface $templateType): bool
    {
        return $templateType->isShopExtensionTemplate();
    }
}
