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
    public function trimNamespace(string $name): string;

    /**
     * @param string $name
     * @return string
     */
    public function fillNamespace(string $name): string;
}
