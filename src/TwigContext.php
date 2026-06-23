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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;

class TwigContext implements TwigContextInterface
{
    public function __construct(
        private Config $config,
        private string $activeAdminTheme,
    ) {
    }

    public function getIsDebug(): bool
    {
        return ContainerFacade::getParameter('oxid_esales.debug_mode');
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
        return ContainerFacade::get(ActiveThemeServiceInterface::class)->getActiveThemeId();
    }
}
