<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

use Twig\Loader\FilesystemLoader;

class ShopTemplateType implements TemplateTypeInterface
{
    public function __construct(
        private string $name,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getNamespace(): string
    {
        return FilesystemLoader::MAIN_NAMESPACE;
    }

    /**
     * @inheritDoc
     */
    public function getParentNamespace(): string
    {
        return $this->getNamespace();
    }

    /**
     * @inheritDoc
     */
    public function getRelativeFilePath(): string
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getFullyQualifiedName(): string
    {
        return "@{$this->getNamespace()}/{$this->getRelativeFilePath()}";
    }

    public function isShopTemplate(): bool
    {
        return true;
    }

    public function isShopExtensionTemplate(): bool
    {
        return false;
    }

    public function isModuleTemplate(): bool
    {
        return false;
    }

    public function isModuleExtensionTemplate(): bool
    {
        return false;
    }
}
