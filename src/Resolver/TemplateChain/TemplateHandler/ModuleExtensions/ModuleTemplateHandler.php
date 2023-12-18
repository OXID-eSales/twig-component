<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ModuleExtensions;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ChainAppenderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\TemplateTypeCheckerInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class ModuleTemplateHandler implements ChainAppenderInterface, TemplateTypeCheckerInterface
{
    public function __construct(
        private ChainAppenderInterface $chainAppender,
    ) {
    }

    public function addToChain(
        TemplateChain $templateChain,
        TemplateTypeInterface $templateType,
        NamespacedDirectory $directory
    ): TemplateChain {
        if (!$this->canHandle($templateType)) {
            return $templateChain;
        }
        $extension = $this->getExtension($templateType, $directory);
        return $this->chainAppender->addToChain($templateChain, $extension, $directory);
    }

    public function canHandle(TemplateTypeInterface $templateType): bool
    {
        return $templateType->isModuleTemplate();
    }

    private function getExtension(
        TemplateTypeInterface $templateType,
        NamespacedDirectory $directory
    ): ModuleExtensionTemplateType {
        return new ModuleExtensionTemplateType(
            $templateType->getName(),
            $directory->getNamespace(),
            $templateType->getNamespace()
        );
    }
}
