<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateNameConverterInterface;

class TemplateChainAggregate implements TemplateChainInterface
{
    /**
     * @param \OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface[] $templateResolvers
     */
    public function __construct(private array $templateResolvers, private TemplateNameConverterInterface $templateNameConverter)
    {
    }

    /** @inheritDoc */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        foreach ($this->templateResolvers as $templateResolver) {
            $resolvedChain = $templateResolver->getChain(
                $this->templateNameConverter->trimNamespace($templateName)
            );
            $templateChain = \array_merge($templateChain, $resolvedChain);
        }
        return $templateChain;
    }
}
