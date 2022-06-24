<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

interface SortingConfigurationValidatorInterface
{
    public function validateModuleId(
        string $moduleId,
        TemplateChain $templateChain,
        TemplateTypeInterface $template
    ): void;
}
