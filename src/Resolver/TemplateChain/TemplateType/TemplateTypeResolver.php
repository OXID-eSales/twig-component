<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Twig\Loader\FilesystemLoader;

use function preg_match;

class TemplateTypeResolver
{
    public function __construct(private string $templateFullyQualifiedName)
    {
    }

    public function getTemplateType(): TemplateTypeInterface
    {
        [, $namespace, $extendsNamespace, $name,] = $this->parseAsModuleExtensionFullyQualifiedName();
        if ($namespace && $extendsNamespace && $name) {
            return new ModuleExtensionTemplateType($name, $namespace, $extendsNamespace);
        }

        [, $namespace, $themeId, $name,] = $this->parseAsShopExtensionFullyQualifiedName();
        if ($namespace && $themeId && $name) {
            return new ShopExtensionTemplateType($name, $namespace, $themeId);
        }

        [, $namespace, $name] = $this->parseAsBaseTemplateFullyQualifiedName();

        return $this->isShopNamespace($namespace)
            ? new ShopTemplateType($name)
            : new ModuleTemplateType($name, $namespace);
    }

    private function parseAsModuleExtensionFullyQualifiedName(): array
    {
        $pattern = '%^@*([^\s/]+)/extensions/modules/([^\s/]+)/(.+)$%i';
        preg_match($pattern, $this->templateFullyQualifiedName, $matches);

        return $matches;
    }

    private function parseAsShopExtensionFullyQualifiedName(): array
    {
        $pattern = '%^@([^\s/]+)/extensions/themes/([^\s/]+)/(.+)$%i';
        preg_match($pattern, $this->templateFullyQualifiedName, $matches);

        return $matches;
    }

    private function parseAsBaseTemplateFullyQualifiedName(): array
    {
        $pattern = '%^(?:@([^\s/]+)/)?(.*)$%';
        preg_match($pattern, $this->templateFullyQualifiedName, $matches);

        return $matches;
    }

    private function isShopNamespace(string $namespace): bool
    {
        return !$namespace || $namespace === FilesystemLoader::MAIN_NAMESPACE;
    }
}
