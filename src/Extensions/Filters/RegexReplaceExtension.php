<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RegexReplaceExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [new TwigFilter('regex_replace', [$this, 'regexReplace'])];
    }

    /**
     * @param mixed  $subject
     * @param string $pattern
     * @param string $replacement
     *
     * @return string|string[]|null
     */
    public function regexReplace($subject, string $pattern, string $replacement)
    {
        return preg_replace($pattern, $replacement, $subject);
    }
}
