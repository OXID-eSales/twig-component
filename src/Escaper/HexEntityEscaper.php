<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Escaper;

use Twig\Environment;

/**
 * Class HexEntityEscaper
 */
class HexEntityEscaper implements EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'hexentity';
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
        $return = '';

        for ($i = 0; $i < strlen($string); $i++) {
            $return .= '&#x' . bin2hex($string[$i]) . ';';
        }

        return $return;
    }
}
