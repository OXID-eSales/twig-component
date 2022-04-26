<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenParser\TokenParserInterface;

class HasRightsExtension extends AbstractExtension
{
    public function __construct(protected AbstractTokenParser $hasRightsTokenParser)
    {
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [$this->hasRightsTokenParser];
    }
}
