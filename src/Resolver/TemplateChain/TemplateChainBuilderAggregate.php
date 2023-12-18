<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\InitialTemplateResolverInterface;

class TemplateChainBuilderAggregate implements TemplateChainBuilderInterface
{
    public function __construct(
        /** @param TemplateChainBuilderInterface[] $templateChainBuilders */
        private array $templateChainBuilders,
        private TemplateChainValidatorInterface $templateChainValidator,
        private InitialTemplateResolverInterface $initialTemplateResolver,
        private TemplateChainSorterInterface $templateChainSorter,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getChain(TemplateTypeInterface $templateType): TemplateChain
    {
        $templateChain = new TemplateChain();
        foreach ($this->templateChainBuilders as $templateChainBuilder) {
            $templateChain->appendChain(
                $templateChainBuilder->getChain($templateType)
            );
        }
        $this->templateChainValidator->validateTemplateChain($templateChain, $templateType);

        return $this->templateChainSorter->sort(
            $templateChain,
            $this->initialTemplateResolver->getInitialTemplate($templateType)
        );
    }
}
