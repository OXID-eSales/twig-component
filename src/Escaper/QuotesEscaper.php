<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Escaper;

/**
 * Escape unescaped single quotes
 */
class QuotesEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'quotes';
    }

    /**
     * Escape unescaped single quotes
     */
    public function escape(string $string, string $charset): string
    {
        return preg_replace("%(?<!\\\\)'%", "\\'", $string);
    }
}
