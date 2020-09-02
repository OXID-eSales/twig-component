<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateChainResolverInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TemplateHierarchyResolver implements TemplateHierarchyResolverInterface
{
    /** @var ModuleTemplateChainResolverInterface */
    private $moduleTemplateChainResolver;

    /**
     * @var TemplateNameResolverInterface
     */
    private $templateNameResolver;

    public function __construct(
        ModuleTemplateChainResolverInterface $moduleTemplateChainResolver,
        TemplateNameResolverInterface $templateNameResolver
    ) {
        $this->moduleTemplateChainResolver = $moduleTemplateChainResolver;
        $this->templateNameResolver = $templateNameResolver;
    }

    /** @inheritDoc */
    public function getParentForTemplate(string $templateName, string $ancestorTemplateName): string
    {
        $templateName = $this->templateNameResolver->resolve($templateName);

        if (!$this->hasHierarchy($templateName) || $this->lastInHierarchy($templateName)) {
            return $ancestorTemplateName;
        }

        return $this->getParentTemplate($templateName);
    }

    private function lastInHierarchy(string $templateName): bool
    {
        return $this->hasNamespace($templateName)
            && $this->extractNamespace($templateName) === FilesystemLoader::MAIN_NAMESPACE;
    }

    private function hasHierarchy(string $templateName): bool
    {
        return !empty($this->moduleTemplateChainResolver->getChain($this->extractName($templateName)));
    }

    private function getParentTemplate(string $templateName): string
    {
        $namespaceHierarchy = $this->moduleTemplateChainResolver->getChain($this->extractName($templateName));

        $currentPosition = !$this->hasNamespace($templateName)
            ? 0
            : array_search($templateName, $namespaceHierarchy, true);

        return $namespaceHierarchy[++$currentPosition] ?? $this->getTemplateWithMainNamespace($templateName);
    }

    private function hasNamespace(string $name): bool
    {
        return $name[0] === '@';
    }

    private function extractNamespace(string $name): string
    {
        $parts = explode('/', $name);
        return ltrim($parts[0], '@');
    }

    private function extractName(string $name): string
    {
        if (!$this->hasNamespace($name)) {
            return $name;
        }
        $parts = explode('/', $name);
        unset($parts[0]);
        return implode('/', $parts);
    }

    private function getTemplateWithMainNamespace(string $templateName): string
    {
        return '@' . FilesystemLoader::MAIN_NAMESPACE . '/' . $this->extractName($templateName);
    }
}
