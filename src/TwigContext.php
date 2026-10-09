<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;

class TwigContext implements TwigContextInterface
{
    public function __construct(
        private Config $config,
        private string $activeThemeId,
        private string $activeAdminTheme,
    ) {
    }

    public function getIsDebug(): bool
    {
        return ContainerFacade::getParameter('oxid_esales.debug_mode');
    }

    public function getActiveThemeId(): string
    {
        return $this->config->isAdmin() ? $this->getActiveAdminThemeId() : $this->getActiveFrontendThemeId();
    }

    private function getActiveAdminThemeId(): string
    {
        if (!$this->activeAdminTheme) {
            throw new InvalidThemeNameException('Admin theme ID is not configured.');
        }

        return $this->activeAdminTheme;
    }

    private function getActiveFrontendThemeId(): string
    {
        if ($this->activeThemeId === '') {
            throw new InvalidThemeNameException('No active theme found.');
        }

        return $this->activeThemeId;
    }
}
