<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Symfony\Component\Filesystem\Filesystem;

class ModuleTemplateExtensionProcessor implements ChainProcessorInterface
{
    use TemplateFileCheckerTrait;

    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function process(array $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): array
    {
        $extension = $this->getExtensionTemplate($templateType, $directory);
        if ($this->templateFileExists($directory, $extension)) {
            $templateChain[] = $extension->getFullyQualifiedName();
        }
        return $templateChain;
    }

    private function getExtensionTemplate(TemplateTypeInterface $template, NamespacedDirectory $directory): ModuleExtensionTemplateType
    {
        $parentNamespace = $template instanceof (ModuleExtensionTemplateType::class)
            ? $template->getParentNamespace()
            : $template->getNamespace();

        return new ModuleExtensionTemplateType(
            $template->getName(),
            $directory->getNamespace(),
            $parentNamespace
        );
    }
}
