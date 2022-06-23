<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface InitialTemplateResolverInterface
{
    public function getInitialTemplate(TemplateTypeInterface $template): TemplateTypeInterface;
}
