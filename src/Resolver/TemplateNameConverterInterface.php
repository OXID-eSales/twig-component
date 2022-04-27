<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

interface TemplateNameConverterInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function convertToUnqualifiedTemplateName(string $name): string;

    /**
     * @param string $name
     * @return string
     */
    public function convertToFullyQualifiedTemplateName(string $name): string;
}
