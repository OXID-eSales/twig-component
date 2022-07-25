<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
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
    private const TWIG_EXTENSION = '.twig';

    public function __construct(
        private TemplateFileResolverInterface $templateFileResolver,
    ) {
    }

    public function createFromTemplateName(string $templateName): TemplateTypeInterface
    {
        $templateName = $this->getFullNameWithFileExtension($templateName);
        $this->validateTemplateFilename($templateName);

        [, $namespace, $extendsNamespace, $name,] = $this->parseAsModuleExtensionFullyQualifiedName($templateName);
        if ($namespace && $extendsNamespace && $name) {
            return new ModuleExtensionTemplateType($name, $namespace, $extendsNamespace);
        }

        [, $namespace, $themeId, $name,] = $this->parseAsShopExtensionFullyQualifiedName($templateName);
        if ($namespace && $themeId && $name) {
            return new ShopExtensionTemplateType($name, $namespace, $themeId);
        }

        [, $namespace, $name] = $this->parseAsBaseTemplateFullyQualifiedName($templateName);

        return $this->isShopNamespace($namespace)
            ? new ShopTemplateType($name)
            : new ModuleTemplateType($name, $namespace);
    }

    private function parseAsModuleExtensionFullyQualifiedName(string $templateName): array
    {
        $pattern = '%^@*([^\s/]+)/extensions/modules/([^\s/]+)/(.+)$%i';
        preg_match($pattern, $templateName, $matches);

        return $matches;
    }

    private function parseAsShopExtensionFullyQualifiedName(string $templateName): array
    {
        $pattern = '%^@([^\s/]+)/extensions/themes/([^\s/]+)/(.+)$%i';
        preg_match($pattern, $templateName, $matches);

        return $matches;
    }

    private function parseAsBaseTemplateFullyQualifiedName(string $templateName): array
    {
        $pattern = '%^(?:@([^\s/]+)/)?(.*)$%';
        preg_match($pattern, $templateName, $matches);

        return $matches;
    }

    private function isShopNamespace(string $namespace): bool
    {
        return !$namespace || $namespace === FilesystemLoader::MAIN_NAMESPACE;
    }

    private function getFullNameWithFileExtension(string $templateName): string
    {
        return $this->templateFileResolver->getFilename($templateName);
    }

    private function validateTemplateFilename(string $templateName): void
    {
        if (!str_ends_with($templateName, self::TWIG_EXTENSION)) {
            throw new NonTemplateFilenameException("Can not process non-template file '$templateName'.");
        }
    }
}
