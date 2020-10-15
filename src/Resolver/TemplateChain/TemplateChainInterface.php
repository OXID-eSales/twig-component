<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainInterface
{
    /**
     * @param string $templatePath
     * @return string[]
     */
    public function getChain(string $templatePath): array;
}
