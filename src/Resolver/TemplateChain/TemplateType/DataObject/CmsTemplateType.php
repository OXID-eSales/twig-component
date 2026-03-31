<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

readonly class CmsTemplateType implements TemplateTypeInterface
{
    public function __construct(
        private string $fullyQualifiedName,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getFullyQualifiedName();
    }

    /**
     * @inheritDoc
     */
    public function getNamespace(): string
    {
        return 'content';
    }

    /**
     * @inheritDoc
     */
    public function getParentNamespace(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRelativeFilePath(): string
    {
        return $this->getFullyQualifiedName();
    }

    /**
     * @inheritDoc
     */
    public function getFullyQualifiedName(): string
    {
        return $this->fullyQualifiedName;
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
        return false;
    }
}
