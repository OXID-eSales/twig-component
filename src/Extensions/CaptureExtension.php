<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\Twig\TokenParser\CaptureTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

class CaptureExtension extends AbstractExtension
{
    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new CaptureTokenParser()];
    }
}
