<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Escaper;

class HtmlAllEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'htmlall';
    }

    public function escape(string $string, string $charset): string
    {
        return htmlentities($string, ENT_QUOTES, $charset);
    }
}
