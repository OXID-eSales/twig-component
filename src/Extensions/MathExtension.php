<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MathExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cos', 'cos'),
            new TwigFunction('sin', 'sin'),
            new TwigFunction('tan', 'tan'),
            new TwigFunction('exp', 'exp'),
            new TwigFunction('log', 'log'),
            new TwigFunction('log10', 'log10'),
            new TwigFunction('pi', 'pi'),
            new TwigFunction('sqrt', 'sqrt'),
        ];
    }
}
