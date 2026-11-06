<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;

readonly class TwigContext implements TwigContextInterface
{
    public function __construct(
        private bool $debugMode,
        private string $activeThemeId,
    ) {
    }

    public function getIsDebug(): bool
    {
        return $this->debugMode;
    }

    public function getActiveThemeId(): string
    {
        if ($this->activeThemeId === '') {
            throw new InvalidThemeNameException('No active theme found.');
        }

        return $this->activeThemeId;
    }
}
