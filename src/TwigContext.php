<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;

/**
 * Class TwigContext
 *
 * @package OxidEsales\Twig
 */
class TwigContext implements TwigContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UtilsView
     */
    private $utilsView;

    /**
     * @var string
     */
    private $activeAdminTheme;

    public function __construct(Config $config, UtilsView $utilsView, string $activeAdminTheme)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
        $this->activeAdminTheme = $activeAdminTheme;
    }

    /**
     * @return array
     */
    public function getTemplateDirectories(): array
    {
        $templateDirectory = $this->utilsView->getTemplateDirs();

        return array_filter(
            $templateDirectory,
            static function ($directory) {
                return is_dir($directory);
            }
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
