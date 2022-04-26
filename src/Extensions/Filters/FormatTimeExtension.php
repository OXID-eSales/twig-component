<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatTimeLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatTimeExtension extends AbstractExtension
{
    public function __construct(private FormatTimeLogic $formatTimeLogic)
    {
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
        return $this->formatTimeLogic->getFormattedTime($seconds);
    }
}
