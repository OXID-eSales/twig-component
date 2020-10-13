<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;

class TemplateChainResolver implements TemplateChainResolverInterface
{
    /** @var TemplateChainInterface */
    private $templateChain;
    /** @var TemplateNameResolverInterface */
    private $templateNameResolver;

    public function __construct(
        TemplateChainInterface $templateChain,
        TemplateNameResolverInterface $templateNameResolver
    ) {
        $this->templateChain = $templateChain;
        $this->templateNameResolver = $templateNameResolver;
    }

    /** @inheritDoc */
    public function getParent(string $templateName): string
    {
        $templateName = $this->templateNameResolver->resolve($templateName);
        $chain = $this->templateChain->getChain($templateName);
        $position = array_search($templateName, $chain, true);
        return $chain[++$position];
    }

    /** @inheritDoc */
    public function getLastChild(string $templateName): string
    {
        $templateName = $this->templateNameResolver->resolve($templateName);
        $templateChain = $this->templateChain->getChain($templateName);
        return $templateChain[0];
    }

    /** @inheritDoc */
    public function hasParent(string $templateName): bool
    {
        $templateName = $this->templateNameResolver->resolve($templateName);
        $chain = $this->templateChain->getChain($templateName);
        return $templateName !== end($chain);
    }
}
