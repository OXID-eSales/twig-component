<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class ShopChainProcessor implements ChainProcessorInterface
{
    public function __construct(
        private ShopTemplateProcessor $shopTemplateProcessor,
    ) {
    }

    public function process(array $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): array
    {
        return $this->shopTemplateProcessor->process($templateChain, $templateType, $directory);
    }
}
