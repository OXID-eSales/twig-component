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
        return $_COOKIE[$key] ?? null;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getGlobalGet(string $key)
    {
        return $_GET[$key] ?? null;
    }
}
