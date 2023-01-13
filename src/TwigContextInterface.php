<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;

interface TwigContextInterface
{
    public function getIsDebug(): bool;

    /**
     * @throws InvalidThemeNameException
     */
    public function getActiveThemeId(): string;
}
