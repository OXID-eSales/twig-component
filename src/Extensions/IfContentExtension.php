<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IfContentLogic;
use OxidEsales\Twig\TokenParser\IfContentTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

class IfContentExtension extends AbstractExtension
{
    public function __construct(private IfContentLogic $ifContentLogic)
    {
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new IfContentTokenParser()];
    }

    /**
     * @param string|null $sIdent
     * @param string|null $sOxid
     *
     * @return mixed
     */
    public function getContent(string $sIdent = null, string $sOxid = null)
    {
        return $this->ifContentLogic->getContent($sIdent, $sOxid);
    }
}
