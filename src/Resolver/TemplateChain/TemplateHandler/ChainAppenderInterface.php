<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface ChainAppenderInterface
{
    public function addToChain(
        TemplateChain $templateChain,
        TemplateTypeInterface $templateType,
        NamespacedDirectory $directory
    ): TemplateChain;
}
