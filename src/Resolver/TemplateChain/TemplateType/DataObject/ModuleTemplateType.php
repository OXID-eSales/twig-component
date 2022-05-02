<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

class ModuleTemplateType implements TemplateTypeInterface
{
    public function __construct(
        private string $name,
        private string $namespace,
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
        return $this->namespace;
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
}
