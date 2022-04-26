<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Escaper;

use Twig\Environment;

/**
 * Escape non-standard chars, such as ms document quotes
 */
class NonStdEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'nonstd';
    }

    /**
     * Escape non-standard chars, such as ms document quotes
     *
     * @param Environment $environment
     * @param string      $string
     * @param string      $charset
     *
     * @return string
     */
    public function escape(Environment $environment, $string, $charset): string
    {
        $return = '';

        for ($i = 0, $length = strlen($string); $i < $length; $i++) {
            $ord = ord($string[$i]);
            // non-standard char, escape it
            if ($ord >= 126) {
                $return .= '&#' . $ord . ';';
            } else {
                $return .= $string[$i];
            }
        }

        return $return;
    }
}
