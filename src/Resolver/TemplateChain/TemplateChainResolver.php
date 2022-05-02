<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;

class TemplateChainResolver implements TemplateChainResolverInterface
{
    public function __construct(
        private TemplateChainBuilderInterface $templateChainBuilder,
        private TemplateFileResolverInterface $templateFileResolver
    ) {
    }

    /** @inheritDoc */
    public function getParent(string $templateName): string
    {
        $filename = $this->templateFileResolver->getFilename($templateName);
        $chain = $this->templateChainBuilder->getChain($filename);
        $position = array_search($filename, $chain, true);
        return $chain[++$position];
    }

    /** @inheritDoc */
    public function getLastChild(string $templateName): string
    {
        $filename = $this->templateFileResolver->getFilename($templateName);
        $templateChain = $this->templateChainBuilder->getChain($filename);
        return $templateChain[0];
    }

    /** @inheritDoc */
    public function hasParent(string $templateName): bool
    {
        $filename = $this->templateFileResolver->getFilename($templateName);
        $chain = $this->templateChainBuilder->getChain($filename);
        return $filename !== end($chain);
    }
}
