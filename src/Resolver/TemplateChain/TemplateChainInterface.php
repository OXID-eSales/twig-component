<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainInterface
{
    /**
     * @param string $templateName
     * @return string[]
     *
     * @throws UnresolvableTemplateNameException
     */
    public function getChain(string $templateName): array;
}
