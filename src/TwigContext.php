<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;

class TwigContext implements TwigContextInterface
{
    public function __construct(private Config $config, private UtilsView $utilsView, private string $activeAdminTheme)
    {
    }

    /**
     * @return array
     */
    public function getTemplateDirectories(): array
    {
        $templateDirectory = $this->utilsView->getTemplateDirs();

        return array_filter(
            $templateDirectory,
            static fn ($directory) => is_dir($directory)
        );
    }

    /**
     * @return bool
     */
    public function getIsDebug(): bool
    {
        return (bool) $this->config->getConfigParam('iDebug', false);
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->config->getConfigParam('sCompileDir') . '/twig';
    }

    public function getActiveThemeId(): string
    {
        return $this->config->isAdmin()
            ? $this->getActiveAdminThemeId()
            : $this->getActiveFrontendThemeId();
    }

    private function getActiveFrontendThemeId(): string
    {
        return $this->config->getConfigParam('sCustomTheme')
            ?: $this->config->getConfigParam('sTheme');
    }

    private function getActiveAdminThemeId(): string
    {
        return $this->activeAdminTheme;
    }
}
