<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

class TemplateChainAggregate implements TemplateChainInterface
{
    /** @var TemplateChainInterface[] */
    private $templateResolvers;

    public function __construct(array $templateResolvers)
    {
        $this->templateResolvers = $templateResolvers;
    }

    /** @inheritDoc */
    public function getChain(string $templatePath): array
    {
        $templateChain = [];
        foreach ($this->templateResolvers as $templateResolver) {
            $templateChain = \array_merge(
                $templateChain,
                $templateResolver->getChain($templatePath)
            );
        }
        return $templateChain;
    }
}
