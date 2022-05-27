<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

interface TemplateTypeInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getNamespace(): string;

    /**
     * @return string
     */
    public function getParentNamespace(): string;

    /**
     * @return string
     */
    public function getRelativeFilePath(): string;

    /**
     * @return string
     */
    public function getFullyQualifiedName(): string;

    /**
     * @return bool
     */
    public function isShopTemplate(): bool;

    /**
     * @return bool
     */
    public function isShopExtensionTemplate(): bool;

    /**
     * @return bool
     */
    public function isModuleTemplate(): bool;

    /**
     * @return bool
     */
    public function isModuleExtensionTemplate(): bool;
}
