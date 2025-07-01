<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeFactoryInterface;

class TemplateChainResolver implements TemplateChainResolverInterface
{
    private array $lastChildCache = [];

    public function __construct(
        private TemplateChainBuilderInterface $templateChainBuilder,
        private TemplateTypeFactoryInterface $templateTypeFactory,
    ) {
    }

    public function getParent(string $templateName): string
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);
        return $this->templateChainBuilder
            ->getChain($templateType)
            ->getParent($templateType)
            ->getFullyQualifiedName();
    }

    public function getLastChild(string $templateName): string
    {
        if (!isset($this->lastChildCache[$templateName])) {
            $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);
            $this->lastChildCache[$templateName] = $this->templateChainBuilder
                ->getChain($templateType)
                ->getLastChild()
                ->getFullyQualifiedName();
        }

        return $this->lastChildCache[$templateName];
    }

    public function hasParent(string $templateName): bool
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);
        return $this->templateChainBuilder
            ->getChain($templateType)
            ->hasParent($templateType);
    }
}
