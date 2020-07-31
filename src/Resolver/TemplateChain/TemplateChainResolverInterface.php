<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainResolverInterface
{
    /**
     * @param string $templateName
     * @return string
     */
    public function getParent(string $templateName): string;

    /**
     * @param string $templateName
     * @return string
     */
    public function getLastChild(string $templateName): string;

    /**
     * @param string $templateName
     * @return bool
     */
    public function hasParent(string $templateName): bool;
}
