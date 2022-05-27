<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface TemplateChainBuilderInterface
{
    /**
     * @param TemplateTypeInterface $templateType
     * @return TemplateChain
     * @throws TemplateNotInChainException
     */
    public function getChain(TemplateTypeInterface $templateType): TemplateChain;
}
