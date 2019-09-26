<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\SmartWordwrapLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class SmartWordwrapExtension
 *
 * @package OxidEsales\Twig\Extensions\Filters
 * @author  Jędrzej Skoczek
 */
class SmartWordwrapExtension extends AbstractExtension
{
    /**
     * @var SmartWordwrapLogic
     */
    private $smartWordWrapLogic;

    /**
     * SmartWordwrapExtension constructor.
     *
     * @param SmartWordwrapLogic $smartWordWrapLogic
     */
    public function __construct(SmartWordwrapLogic $smartWordWrapLogic)
    {
        $this->smartWordWrapLogic = $smartWordWrapLogic;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [new TwigFilter('smart_wordwrap', [$this, 'smartWordwrap'], array('is_safe' => array('html')))];
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
