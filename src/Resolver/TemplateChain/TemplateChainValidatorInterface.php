<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface TemplateChainValidatorInterface
{
    /**
     * @param TemplateChain $templateChain
     * @param TemplateTypeInterface $templateType
     * @return void
     */
    public function validateTemplateChain(TemplateChain $templateChain, TemplateTypeInterface $templateType): void;
}
