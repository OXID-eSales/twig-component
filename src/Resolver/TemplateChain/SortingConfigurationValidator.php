<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class SortingConfigurationValidator implements SortingConfigurationValidatorInterface
{
    public function validateModuleId(
        string $moduleId,
        TemplateChain $templateChain,
        TemplateTypeInterface $template
    ): void {
        $this->validateWithExtendedTemplate($template, $moduleId);
        $this->validateWithExtensionChain($templateChain, $moduleId);
    }

    private function validateWithExtendedTemplate(TemplateTypeInterface $template, string $moduleId): void
    {
        if ($template->isModuleTemplate() && $template->getNamespace() === $moduleId) {
            throw new InvalidSortingConfigurationException(
                "Template for module '$moduleId' should not extended itself!"
            );
        }
    }

    private function validateWithExtensionChain(TemplateChain $templateChain, string $moduleId): void
    {
        if (!$templateChain->hasModuleId($moduleId)) {
            throw new InvalidSortingConfigurationException(
                "Template chain does not contain template for module '$moduleId'."
            );
        }
    }
}
