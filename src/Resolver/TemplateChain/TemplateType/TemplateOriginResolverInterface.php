<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface TemplateOriginResolverInterface
{
    public function getTemplateOrigin(TemplateTypeInterface $template): TemplateTypeInterface;
}
