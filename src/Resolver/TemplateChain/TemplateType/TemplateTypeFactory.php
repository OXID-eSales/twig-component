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
use function str_ends_with;

class TemplateTypeFactory implements TemplateTypeFactoryInterface
{
    private const TWIG_FILE_EXTENSION = '.twig';
    private const MODULE_EXTENSION_TEMPLATE_TYPE_PATTERN = '%^@*([^\s/]+)/extensions/modules/([^\s/]+)/(.+)$%i';
    private const SHOP_EXTENSION_TEMPLATE_TYPE_PATTERN = '%^@([^\s/]+)/extensions/themes/([^\s/]+)/(.+)$%i';
    private const BASE_TEMPLATE_TYPE_PATTERN = '%^(?:@([^\s/]+)/)?(.*)$%';

    public function createFromTemplateName(string $templateName): TemplateTypeInterface
    {
        $this->validateTemplateFilename($templateName);

        if ($this->isModuleExtensionFullyQualifiedName($templateName)) {
            [, $namespace, $extendsNamespace, $name,] = $this->parseAsModuleExtensionFullyQualifiedName($templateName);
            if ($namespace && $extendsNamespace && $name) {
                return new ModuleExtensionTemplateType($name, $namespace, $extendsNamespace);
            }
        }

        if ($this->isShopExtensionFullyQualifiedName($templateName)) {
            [, $namespace, $themeId, $name,] = $this->parseAsShopExtensionFullyQualifiedName($templateName);
            if ($namespace && $themeId && $name) {
                return new ShopExtensionTemplateType($name, $namespace, $themeId);
            }
        }

        [, $namespace, $name] = $this->parseAsBaseTemplateFullyQualifiedName($templateName);

        return $this->isShopNamespace($namespace)
            ? new ShopTemplateType($name)
            : new ModuleTemplateType($name, $namespace);
    }

    private function isModuleExtensionFullyQualifiedName(string $templateName): bool
    {
        return preg_match(self::MODULE_EXTENSION_TEMPLATE_TYPE_PATTERN, $templateName) === 1;
    }

    private function parseAsModuleExtensionFullyQualifiedName(string $templateName): array
    {
        preg_match(self::MODULE_EXTENSION_TEMPLATE_TYPE_PATTERN, $templateName, $matches);

        return $matches;
    }

    private function isShopExtensionFullyQualifiedName(string $templateName): bool
    {
        return preg_match(self::SHOP_EXTENSION_TEMPLATE_TYPE_PATTERN, $templateName) === 1;
    }

    private function parseAsShopExtensionFullyQualifiedName(string $templateName): array
    {
        preg_match(self::SHOP_EXTENSION_TEMPLATE_TYPE_PATTERN, $templateName, $matches);

        return $matches;
    }

    private function parseAsBaseTemplateFullyQualifiedName(string $templateName): array
    {
        preg_match(self::BASE_TEMPLATE_TYPE_PATTERN, $templateName, $matches);

        return $matches;
    }

    private function isShopNamespace(string $namespace): bool
    {
        return !$namespace || $namespace === FilesystemLoader::MAIN_NAMESPACE;
    }

    private function validateTemplateFilename(string $templateName): void
    {
        if (!str_ends_with($templateName, self::TWIG_FILE_EXTENSION)) {
            throw new NonTemplateFilenameException("Can not process non-template file '$templateName'.");
        }
    }
}
