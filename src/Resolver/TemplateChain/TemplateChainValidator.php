<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateNameConverterInterface;

class TemplateChainValidator implements TemplateChainValidatorInterface
{
    public function __construct(private TemplateChainInterface $templateChain, private TemplateNameConverterInterface $templateNameConverter)
    {
    }

    /** @inheritDoc */
    public function isInChain(string $templateName): void
    {
        $pathWithNamespace = $this->templateNameConverter->fillNamespace($templateName);
        $templateChain = $this->templateChain->getChain($pathWithNamespace);
        if (!\in_array($pathWithNamespace, $templateChain)) {
            throw new TemplateNotInChainException(
                "Template name `$templateName` not found in inheritance chain."
            );
        }
    }
}
