<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\SmartWordwrapLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SmartWordwrapExtension extends AbstractExtension
{
    public function __construct(private SmartWordwrapLogic $smartWordWrapLogic)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [new TwigFilter('smart_wordwrap', [$this, 'smartWordwrap'])];
    }

    /**
     * @param string $string
     * @param int    $length
     * @param string $break
     * @param int    $cutRows
     * @param int    $tolerance
     * @param string $etc
     *
     * @return string
     */
    public function smartWordwrap($string, $length = 80, $break = "\n", $cutRows = 0, $tolerance = 0, $etc = '...')
    {
        return $this->smartWordWrapLogic->wrapWords($string, $length, $break, $cutRows, $tolerance, $etc);
    }
}
