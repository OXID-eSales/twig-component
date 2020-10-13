<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainValidatorInterface
{
    /**
     * @param string $templateName
     * @throws TemplateNotInChainException
     */
    public function isInChain(string $templateName): void;
}
