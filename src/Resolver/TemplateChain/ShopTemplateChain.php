<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplatePathConverterInterface;
use OxidEsales\Twig\TwigContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Loader\FilesystemLoader;
use Webmozart\PathUtil\Path;

class ShopTemplateChain implements TemplateChainInterface
{
    /** @var TwigContextInterface */
    private $twigContext;
    /** @var Filesystem */
    private $filesystem;
    /** @var TemplatePathConverterInterface */
    private $templatePathConverter;

    public function __construct(
        TwigContextInterface $twigContext,
        Filesystem $filesystem,
        TemplatePathConverterInterface $templatePathConverter
    ) {
        $this->twigContext = $twigContext;
        $this->filesystem = $filesystem;
        $this->templatePathConverter = $templatePathConverter;
    }

    /** @inheritDoc */
    public function getChain(string $templatePath): array
    {
        if (
            $this->isShopTemplateWithoutNamespace($templatePath)
            || $this->isShopTemplateWithMainNamespace($templatePath)
            || $this->isModuleTemplateExtendingShop($templatePath)
        ) {
            return [
                $this->templatePathConverter->fillNamespace(
                    $this->convertToShopTemplatePath($templatePath)
                )
            ];
        }
        return [];
    }

    private function shopHasTemplate(string $templateName): bool
    {
        foreach ($this->twigContext->getTemplateDirectories() as $directory) {
            $path = Path::join($directory, $templateName);
            if ($this->filesystem->exists($path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $templatePath
     * @return bool
     */
    private function isShopTemplateWithoutNamespace(string $templatePath): bool
    {
        return !$this->templatePathConverter->hasNamespace($templatePath) && $this->shopHasTemplate($templatePath);
    }

    /**
     * @param string $templatePath
     * @return bool
     */
    private function isShopTemplateWithMainNamespace(string $templatePath): bool
    {
        return $this->templatePathConverter->getNamespace($templatePath) === FilesystemLoader::MAIN_NAMESPACE;
    }

    /**
     * @param string $templatePath
     * @return bool
     */
    private function isModuleTemplateExtendingShop(string $templatePath): bool
    {
        return $this->templatePathConverter->extendsNamespace($templatePath)
            && $this->templatePathConverter->getExtendedNamespace($templatePath) === 'shop';
    }

    /**
     * @param string $templatePath
     * @return string
     */
    private function convertToShopTemplatePath(string $templatePath): string
    {
        return $this->isModuleTemplateExtendingShop($templatePath)
            ? $this->templatePathConverter->trimNamespaceAndExtends($templatePath)
            : $templatePath;
    }
}
