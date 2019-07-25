<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class EncloseExtension
 *
 * @package OxidEsales\Twig\Extensions
 * @author  Jędrzej Skoczek
 */
class EncloseExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [new TwigFilter('enclose', [$this, 'enclose'])];
    }

    /**
     * @param string $string
     * @param string $encloser
     *
     * @return string
     */
    public function enclose(string $string, string $encloser = ""): string
    {
        return $encloser . $string . $encloser;
    }
}
