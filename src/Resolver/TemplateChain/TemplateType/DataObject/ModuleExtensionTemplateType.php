<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

class ModuleExtensionTemplateType implements TemplateTypeInterface
{
    public function __construct(
        private string $name,
        private string $namespace,
        private string $parentNamespace,
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
        return $this->namespace;
    }

    /**
     * @inheritDoc
     */
    public function getParentNamespace(): string
    {
        return $this->parentNamespace;
    }

    /**
     * @inheritDoc
     */
    public function getRelativeFilePath(): string
    {
        return "extensions/modules/{$this->getParentNamespace()}/{$this->getName()}";
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
        return false;
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
        return true;
    }
}
