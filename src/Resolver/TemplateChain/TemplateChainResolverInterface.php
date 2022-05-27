<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

interface TemplateChainResolverInterface
{
    public function getParent(string $templateName): string;

    public function getLastChild(string $templateName): string;

    public function hasParent(string $templateName): bool;
}
