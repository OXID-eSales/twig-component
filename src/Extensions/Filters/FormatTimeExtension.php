<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\FormatTimeLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class FormatTimeExtension
 *
 * @package OxidEsales\Twig\Filters
 * @author  Jędrzej Skoczek
 */
class FormatTimeExtension extends AbstractExtension
{
    /**
     * @var FormatTimeLogic
     */
    private $formatTimeLogic;

    /**
     * FormatTimeExtension constructor.
     *
     * @param FormatTimeLogic $formatTimeLogic
     */
    public function __construct(FormatTimeLogic $formatTimeLogic)
    {
        $this->formatTimeLogic = $formatTimeLogic;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [new TwigFilter('format_time', [$this, 'formatTime'])];
    }

    /**
     * @param int $seconds
     *
     * @return string
     */
    public function formatTime(int $seconds): string
    {
        $formattedTime = $this->formatTimeLogic->getFormattedTime($seconds);

        return $formattedTime;
    }
}
