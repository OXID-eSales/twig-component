<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject;

interface ThemedTemplateTypeInterface
{
    /**
     * @return string
     */
    public function getThemeId(): string;
}
