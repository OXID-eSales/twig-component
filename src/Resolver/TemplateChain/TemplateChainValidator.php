<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeResolver;

use function in_array;

class TemplateChainValidator implements TemplateChainValidatorInterface
{
    /** @inheritDoc */
    public function validateTemplateChain(array $templateChain, string $templateName): void
    {
        $template = (new TemplateTypeResolver($templateName))->getTemplateType();
        $templateNameInChain = $this->getTemplateNameInChain($template);
        if (!in_array($templateNameInChain, $templateChain, true)) {
            throw new TemplateNotInChainException(
                "Error building inheritance chain for the template `$templateName`."
            );
        }
    }

    private function getTemplateNameInChain(TemplateType\DataObject\TemplateTypeInterface $templateType): string
    {
        return $templateType instanceof (ShopTemplateType::class)
            ? $templateType->getRelativeFilePath()
            : $templateType->getFullyQualifiedName();
    }
}
