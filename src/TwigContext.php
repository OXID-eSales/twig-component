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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

class TwigContext implements TwigContextInterface
{
    public function __construct(
        private Config $config,
        private ActiveThemeProviderInterface $activeThemeProvider,
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
        try {
            return $this->activeThemeProvider->getActiveThemeId($this->config->getShopId());
        } catch (ActiveThemeNotFoundException $exception) {
            throw new InvalidThemeNameException('No active theme found.', previous: $exception);
        }
    }
}
