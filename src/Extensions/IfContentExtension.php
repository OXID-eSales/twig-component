<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IfContentLogic;
use OxidEsales\Twig\TokenParser\IfContentTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

/**
 * Class IfContentExtension
 */
class IfContentExtension extends AbstractExtension
{
    /**
     * @var IfContentLogic
     */
    private $ifContentLogic;

    /**
     * IfContentExtension constructor.
     *
     * @param IfContentLogic $ifContentLogic
     */
    public function __construct(IfContentLogic $ifContentLogic)
    {
        $this->ifContentLogic = $ifContentLogic;
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new IfContentTokenParser()];
    }

    /**
     * @param string $sIdent
     * @param string $sOxid
     *
     * @return mixed
     */
    public function getContent(string $sIdent = null, string $sOxid = null)
    {
        return $this->ifContentLogic->getContent($sIdent, $sOxid);
    }
}
