<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

interface TemplatePathConverterInterface
{
    /**
     * @param string $path
     * @return string
     */
    public function trimNamespaceAndExtends(string $path): string;

    /**
     * @param string $path
     * @return string
     */
    public function fillNamespace(string $path): string;

    /**
     * @param string $path
     * @return bool
     */
    public function hasNamespace(string $path): bool;

    /**
     * @param string $path
     * @return string
     */
    public function getNamespace(string $path): string;

    /**
     * @param string $path
     * @return bool
     */
    public function extendsNamespace(string $path): bool;

    /**
     * @param string $path
     * @return bool
     */
    public function getExtendedNamespace(string $path): string;
}
