<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface TemplateTypeCheckerInterface
{
    public function canHandle(TemplateTypeInterface $templateType): bool;
}
