<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateNameConverterInterface;

use function array_merge;

class TemplateChainAggregate implements TemplateChainInterface
{
    private string $originalTemplateName;

    /**
     * @param \OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface[] $templateResolvers
     */
    public function __construct(
        private array $templateResolvers,
        private TemplateNameConverterInterface $templateNameConverter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getChain(string $templateName): array
    {
        $templateChains = [];
        $this->originalTemplateName = $templateName;
        $unqualifiedTemplateName = $this->templateNameConverter->convertToUnqualifiedTemplateName($this->originalTemplateName);
        foreach ($this->templateResolvers as $templateResolver) {
            $templateChains[] = $templateResolver->getChain($unqualifiedTemplateName);
        }
        $aggregatedChain = array_merge(... $templateChains);
        $this->validateAggregatedTemplateChain($aggregatedChain);

        return $aggregatedChain;
    }

    /**
     * @param array $templateChain
     * @return void
     * @throws UnresolvableTemplateNameException
     */
    private function validateAggregatedTemplateChain(array $templateChain): void
    {
        if (empty($templateChain)) {
            throw new UnresolvableTemplateNameException(
                "Error building template chain for '$this->originalTemplateName'. Template name can not be resolved to any known file."
            );
        }
    }
}
