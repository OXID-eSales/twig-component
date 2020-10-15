<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use OxidEsales\Twig\Resolver\TemplatePathConverterInterface;
use OxidEsales\Twig\TwigContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Loader\FilesystemLoader;
use Webmozart\PathUtil\Path;

class ModulesTemplateChain implements TemplateChainInterface
{
    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    /**
     * @var ModulesTemplateDirectoryResolverInterface
     */
    private $moduleTemplateDirectoryResolver;

    /**
     * @var TwigContextInterface
     */
    private $twigContext;

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var TemplatePathConverterInterface
     */
    private $templatePathConverter;

    public function __construct(
        ActiveModulesDataProviderInterface $activeModulesDataProvider,
        ModulesTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolver,
        TwigContextInterface $twigContext,
        Filesystem $filesystem,
        TemplatePathConverterInterface $templatePathConverter
    ) {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->moduleTemplateDirectoryResolver = $moduleTemplateDirectoryResolver;
        $this->twigContext = $twigContext;
        $this->filesystem = $filesystem;
        $this->templatePathConverter = $templatePathConverter;
    }

    /** @inheritDoc */
    public function getChain(string $templatePath): array
    {
        $templateChain = [];

        if ($this->extendsShopTemplate($templatePath)) {
            $templateChain = $this->getChainForShopExtensions($templatePath);
        } elseif ($this->isModuleNamespace($templatePath)) {
            $templateChain = $this->getChainForModuleTemplate($templatePath);
        }

        return $templateChain;
    }

    private function getAbsoluteTemplatePathForTheme(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $this->twigContext->getActiveThemeId(),
            $templateName
        );
    }

    private function getAbsoluteTemplatePath(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $templateName
        );
    }

    private function moduleHasTemplateForActiveTheme(string $moduleId, string $templateName): bool
    {
        return $this->filesystem->exists($this->getAbsoluteTemplatePathForTheme($moduleId, $templateName));
    }

    private function moduleHasTemplate(string $moduleId, string $templateName): bool
    {
        return $this->filesystem->exists($this->getAbsoluteTemplatePath($moduleId, $templateName));
    }

    public function fillNamespace(string $path): string
    {
        if ($this->templatePathConverter->hasNamespace($path)) {
            return $path;
        }
        return sprintf('@%s/%s', FilesystemLoader::MAIN_NAMESPACE, $path);
    }

    public function getNamespace(string $path): string
    {
        $parts = explode('/', $path);
        return $parts[0];
    }

    public function extendsNamespace(string $path): bool
    {
        return strpos($path, 'extensions' . '/') !== false;
    }

    public function extendsShopNamespace(string $path): bool
    {
        return strpos($path, 'extensions/shop/') !== false;
    }

    public function getTemplateNameWithoutNamespace(string $name): string
    {
        if (!$this->templatePathConverter->hasNamespace($name)) return $name;

        return str_replace($this->getNamespace($name) . '/', '', $name);
    }

    private function getChainForShopExtensions(string $templatePath): array
    {
        $templatePath = $this->extendsNamespace($templatePath)
            ? $this->getTemplateNameWithoutNamespace($templatePath)
            : 'extensions/shop/' . $templatePath;
        return $this->getTemplateExtensionsFromModules($templatePath);
    }

    private function extendsShopTemplate(string $templatePath): bool
    {
        return !$this->templatePathConverter->hasNamespace($templatePath) || $this->extendsShopNamespace($templatePath);
    }

    private function isModuleNamespace(string $templatePath): bool
    {
        return $this->templatePathConverter->hasNamespace($templatePath) && in_array(
                ltrim($this->getNamespace($templatePath), '@'),
                $this->activeModulesDataProvider->getModuleIds());
    }

    private function getChainForModuleTemplate(string $templatePath): array
    {
        if ($this->isChildModuleTemplate($templatePath)) {
            $templatePath = $this->getTemplateNameWithoutNamespace($templatePath);
            $templateChain = $this->getTemplateExtensionsFromModules($templatePath);

            $templateChain[] = $this->getMainModuleParentTemplatePath($templatePath);
        } else {
            $templateChain = $this->getTemplateExtensionsFromModules($this->getModuleChildTemplatePath($templatePath));
            $templateChain[] = $templatePath;
        }
        return $templateChain;
    }

    private function getTemplateExtensionsFromModules(string $templatePath): array
    {
        $templateChain = [];
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            if ($this->moduleHasTemplateForActiveTheme($moduleId, $templatePath)) {
                $templateChain[] = "@$moduleId/{$this->twigContext->getActiveThemeId()}/$templatePath";
            } elseif ($this->moduleHasTemplate($moduleId, $templatePath)) {
                $templateChain[] = "@$moduleId/$templatePath";
            }
        }
        return $templateChain;
    }

    private function getMainModuleParentTemplatePath(string $templatePath): string
    {
        $parts = explode('/', $templatePath);
        $moduleId = $parts[1];

        return '@' . $moduleId . '/' . $this->templatePathConverter->trimNamespaceAndExtends($templatePath);
    }

    private function getModuleIdFromNamespacedTemplatePath(string $templatePath)
    {
        return str_replace('@', '', $this->getNamespace($templatePath));
    }

    private function getModuleChildTemplatePath(string $templatePath): string
    {
        $templatePath = Path::join(
            'extensions',
            $this->getModuleIdFromNamespacedTemplatePath($templatePath),
            $this->getTemplateNameWithoutNamespace($templatePath)
        );
        return $templatePath;
    }

    private function isChildModuleTemplate(string $templatePath): bool
    {
        return $this->extendsNamespace($templatePath);
    }
}
