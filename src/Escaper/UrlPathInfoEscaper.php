<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Escaper;

class UrlPathInfoEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'urlpathinfo';
    }

    public function escape(string $string, string $charset): string
    {
        return str_replace('%2F', '/', rawurlencode($string));
    }
}
