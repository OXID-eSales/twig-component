<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

interface TwigContextInterface
{
    public function getIsDebug(): bool;

    public function getActiveThemeId(): string;
}
