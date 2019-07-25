<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

/**
 * Interface TwigEngineConfigurationInterface
 *
 * @package OxidEsales\Twig
 */
interface TwigEngineConfigurationInterface
{
    /**
     * @return array
     */
    public function getParameters(): array;
}
