<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TruncateLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TruncateExtension extends AbstractExtension
{
    public function __construct(private TruncateLogic $truncateLogic)
    {
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncate'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string|null $string $string
     * @param int $length
     * @param string $suffix
     * @param bool $breakWords
     * @param bool $middle
     *
     * @return string
     */
    public function truncate(string $string = null, int $length = 80, string $suffix = '...', bool $breakWords = false, bool $middle = false): string
    {
        return $this->truncateLogic->truncate($string, $length, $suffix, $breakWords, $middle);
    }
}
