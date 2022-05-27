<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class TemplateChainValidator implements TemplateChainValidatorInterface
{
    /** @inheritDoc */
    public function validateTemplateChain(TemplateChain $templateChain, TemplateTypeInterface $templateType): void
    {
        if (!$templateChain->has($templateType)) {
            throw new TemplateNotInChainException(
                "Error building inheritance chain for the template `{$templateType->getFullyQualifiedName()}`."
            );
        }
    }
}
