<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainBuilderInterface
{
    /**
     * @param string $templateName
     * @return string[]
     *
     * @throws TemplateNotInChainException
     */
    public function getChain(string $templateName): array;
}
