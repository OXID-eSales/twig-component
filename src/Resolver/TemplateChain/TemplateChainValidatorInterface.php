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
     * @param array $templateChain
     * @return void
     */
    public function validateTemplateChain(array $templateChain, string $templateName): void;
}
