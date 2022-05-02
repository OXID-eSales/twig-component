<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

interface TwigContextInterface
{
    /**
     * @return boolean
     */
    public function getIsDebug(): bool;

    /**
     * @return string
     */
    public function getCacheDir(): string;

    /**
     * @return string
     */
    public function getActiveThemeId(): string;
}
