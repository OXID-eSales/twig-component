<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainValidatorInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeResolver;

class TokenValueValidator implements TokenValueValidatorInterface
{
    public function __construct(
        private TemplateChainBuilderInterface $templateChainBuilder,
        private TemplateChainValidatorInterface $templateChainValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function isChainableTemplateName(string $templateName): void
    {
        $fullyQualifiedName = (new TemplateTypeResolver($templateName))
            ->getTemplateType()
            ->getFullyQualifiedName();

        $this->templateChainValidator->validateTemplateChain(
            $this->templateChainBuilder->getChain($fullyQualifiedName),
            $fullyQualifiedName
        );
    }
}
