<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

interface TemplateHierarchyResolverInterface
{
    /**
     * @param string $templateName
     * @param string $ancestorTemplateName
     * @return string
     */
    public function getParentForTemplate(string $templateName, string $ancestorTemplateName): string;
}
