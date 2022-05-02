<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\TwigContextInterface;
use Symfony\Component\Filesystem\Filesystem;

class ShopTemplateExtensionProcessor implements ChainProcessorInterface
{
    use TemplateFileCheckerTrait;

    public function __construct(
        private TwigContextInterface $twigContext,
        private Filesystem $filesystem,
    ) {
    }

    public function process(array $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): array
    {
        foreach ($this->getSupportedThemes() as $theme) {
            $extension = $this->getExtensionTemplate($templateType, $directory, $theme);
            if ($this->templateFileExists($directory, $extension)) {
                $templateChain[] = $extension->getFullyQualifiedName();
                break;
            }
        }
        return $templateChain;
    }

    private function getExtensionTemplate(TemplateTypeInterface $template, NamespacedDirectory $directory, string $theme): ShopExtensionTemplateType
    {
        return new ShopExtensionTemplateType(
            $template->getName(),
            $directory->getNamespace(),
            $theme
        );
    }

    private function getSupportedThemes(): array
    {
        return [
            $this->twigContext->getActiveThemeId(),
            'default',
        ];
    }
}
