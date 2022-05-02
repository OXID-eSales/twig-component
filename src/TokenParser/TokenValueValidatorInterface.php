<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;

interface TokenValueValidatorInterface
{
    /**
     * @param string $templateName
     * @return void
     * @throws TemplateNotInChainException
     */
    public function isChainableTemplateName(string $templateName): void;
}
