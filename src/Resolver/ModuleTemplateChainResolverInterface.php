<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

interface ModuleTemplateChainResolverInterface
{
    /**
     * @param string $templateName
     * @return string[]
     */
    public function getChain(string $templateName): array;
}
