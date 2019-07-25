<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class GetRequestVariablesExtension
 *
 * @package OxidEsales\Twig\Extensions
 * @author  Jędrzej Skoczek
 */
class GetRequestVariablesExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_global_cookie', [$this, 'getGlobalCookie']),
            new TwigFunction('get_global_get', [$this, 'getGlobalGet'])
        ];
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getGlobalCookie(string $key)
    {
        $cookie = null;
        if (isset($_COOKIE[$key])) {
            $cookie = $_COOKIE[$key];
        }
        return $cookie;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getGlobalGet(string $key)
    {
        $get = null;
        if (isset($_GET[$key])) {
            $get = $_GET[$key];
        }
        return $get;
    }
}
