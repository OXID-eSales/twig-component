<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheInterface;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheNotFoundException;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeFactoryInterface;

class TemplateChainResolver implements TemplateChainResolverInterface
{
    public function __construct(
        private TemplateChainBuilderInterface $templateChainBuilder,
        private TemplateTypeFactoryInterface $templateTypeFactory,
        private TemplateChainCacheInterface $templateChainCache,
    ) {
    }

    public function getParent(string $templateName): string
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);
        $templateChain = $this->getTemplateChain($templateName);

        return $templateChain->getParent($templateType)->getFullyQualifiedName();
    }

    public function getLastChild(string $templateName): string
    {
        return $this->getTemplateChain($templateName)->getLastChild()->getFullyQualifiedName();
    }

    public function hasParent(string $templateName): bool
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);

        return $this->getTemplateChain($templateName)->hasParent($templateType);
    }

    private function getTemplateChain(string $templateName): TemplateChain
    {
        try {
            return $this->templateChainCache->get($templateName);
        } catch (TemplateChainCacheNotFoundException) {
            $templateChain = $this->createTemplateChain($templateName);
            $this->templateChainCache->put($templateName, $templateChain);

            return $templateChain;
        }
    }

    private function createTemplateChain(string $templateName): TemplateChain
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);

        return $this->templateChainBuilder->getChain($templateType);
    }
}
