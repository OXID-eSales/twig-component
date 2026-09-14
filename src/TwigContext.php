<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class TwigContext implements TwigContextInterface
{
    public function __construct(
        private ContextInterface $context,
        private bool $debugMode,
        private string $activeThemeId,
        private string $adminThemeName,
    ) {
    }

    public function getIsDebug(): bool
    {
        return $this->debugMode;
    }

    public function getActiveThemeId(): string
    {
        return $this->context->isAdmin() ? $this->getAdminThemeName() : $this->getActiveFrontendThemeId();
    }

    private function getAdminThemeName(): string
    {
        if ($this->adminThemeName === '') {
            throw new InvalidThemeNameException('Admin theme name is not configured.');
        }

        return $this->adminThemeName;
    }

    private function getActiveFrontendThemeId(): string
    {
        if ($this->activeThemeId === '') {
            throw new InvalidThemeNameException('No active theme found.');
        }

        return $this->activeThemeId;
    }
}
