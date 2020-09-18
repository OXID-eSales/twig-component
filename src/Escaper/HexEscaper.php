<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Escaper;

use Twig\Environment;

/**
 * Class HexEscaper
 *
 * Escape every character into hex
 */
class HexEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'hex';
    }

    /**
     * Escape every character into hex
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

        for ($i = 0, $stringLength = strlen($string); $i < $stringLength; $i++) {
            $return .= '%' . bin2hex($string[$i]);
        }

        return $return;
    }
}
