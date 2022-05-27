<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class TemplateChainBuilderAggregate implements TemplateChainBuilderInterface
{
    public function __construct(
        /** @param TemplateChainBuilderInterface[] $templateChainBuilders */
        private array $templateChainBuilders,
        private TemplateChainValidatorInterface $templateChainValidator,
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
        return $templateChain;
    }
}
