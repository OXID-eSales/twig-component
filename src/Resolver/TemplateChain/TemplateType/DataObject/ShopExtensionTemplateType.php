<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

use Twig\Loader\FilesystemLoader;

class ShopExtensionTemplateType implements TemplateTypeInterface, ThemedTemplateTypeInterface
{
    public function __construct(
        private string $name,
        private string $namespace,
        private string $themeId,
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
        return FilesystemLoader::MAIN_NAMESPACE;
    }

    /**
     * @inheritDoc
     */
    public function getRelativeFilePath(): string
    {
        return "extensions/themes/{$this->getThemeId()}/{$this->getName()}";
    }

    /**
     * @inheritDoc
     */
    public function getFullyQualifiedName(): string
    {
        return "@{$this->getNamespace()}/{$this->getRelativeFilePath()}";
    }

    /**
     * @inheritDoc
     */
    public function getThemeId(): string
    {
        return $this->themeId;
    }
}
