<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Escaper;

use Twig\Environment;

/**
 * Class HtmlAllEscaper
 */
class HtmlAllEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'htmlall';
    }

    /**
     * @param Environment $environment
     * @param string      $string
     * @param string      $charset
     *
     * @return string
     */
    public function escape(Environment $environment, $string, $charset): string
    {
        return htmlentities($string, ENT_QUOTES, $charset);
    }
}
