<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;

class TwigContext implements TwigContextInterface
{
    public function __construct(
        private Config $config,
        private string $activeAdminTheme,
    ) {
    }

    public function getIsDebug(): bool
    {
        return (bool) $this->config->getConfigParam('iDebug', false);
    }

    public function getActiveThemeId(): string
    {
        $themeId = $this->config->isAdmin() ? $this->activeAdminTheme : $this->getActiveFrontendThemeId();
        if (!$themeId) {
            throw new InvalidThemeNameException('Theme ID is not configured.');
        }
        return $themeId;
    }

    private function getActiveFrontendThemeId(): string
    {
        $theme = $this->config->getConfigParam('sCustomTheme') ?: $this->config->getConfigParam('sTheme');

        return (string)$theme;
    }
}
