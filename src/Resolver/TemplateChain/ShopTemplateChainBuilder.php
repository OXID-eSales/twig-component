<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor\ChainProcessorInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeResolver;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;

class ShopTemplateChainBuilder implements TemplateChainBuilderInterface
{
    public function __construct(
        private TemplateDirectoryResolverInterface $templateDirectoryResolver,
        private ChainProcessorInterface $shopChainProcessor,
    ) {
    }

    /** @inheritDoc */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        $templateType = (new TemplateTypeResolver($templateName))->getTemplateType();
        foreach ($this->templateDirectoryResolver->getTemplateDirectories() as $directory) {
            $templateChain = $this->shopChainProcessor->process($templateChain, $templateType, $directory);
        }

        return $templateChain;
    }

}
