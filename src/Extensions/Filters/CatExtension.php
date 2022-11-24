<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CatExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [new TwigFilter('cat', [$this, 'cat'], ['deprecated' => true, 'alternative' => '~'])];
    }

    /**
     * @param string|null $string
     * @param string|null $cat
     *
     * @return string
     */
    public function cat(string $string = null, string $cat = null): string
    {
        return $string . $cat;
    }
}
