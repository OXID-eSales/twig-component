<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use Symfony\Component\Filesystem\Path;

trait TestingFixturesTrait
{
    use ContainerTrait;

    private string $fixtureRoot = __DIR__;
    private string $currentTheme = 'default';

    public function initFixtures(string $fixtureRoot): void
    {
        $this->fixtureRoot = $fixtureRoot;
        Registry::getConfig()->reinitialize();
    }

    public function setupModuleFixture(string $moduleId): void
    {
        $this->installModuleFixture($moduleId);
        $this->activateModuleFixture($moduleId);
        $this->destroyTestContainer();
    }

    public function uninstallModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getPackageFixture($moduleId));
        $this->destroyTestContainer();
    }

    public function deactivateModuleFixture(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
        $this->destroyTestContainer();
    }

    public function setShopSourceFixture(): void
    {
        $this->setParameter(
            'oxid_esales.shop_source_directory',
            "{$this->getFixturesDirectory()}/shop/source/"
        );
    }

    public function reloadTestContainer(): void
    {
        $this->setShopSourceFixture();
        $this->setThemeFixture($this->currentTheme);
    }

    public function setThemeFixture(string $themeId): void
    {
        $this->installThemeFixture($themeId);
    }

    public function setChildThemeFixture(string $themeId): void
    {
        $this->installThemeFixture($themeId);
    }

    private function installThemeFixture(string $themeId): void
    {
        $context = $this->get(BasicContextInterface::class);
        $shopId = $context->getDefaultShopId();
        $themePath = "{$this->getFixturesDirectory()}/shop/source/Application/views/$themeId";

        if (!$this->isThemeConfiguredFromSource($themeId, $shopId, $themePath)) {
            $this->get(ThemeConfigurationInstallerInterface::class)->install($themePath);
        }
        $this->get(ThemeActivationServiceInterface::class)->activate($themeId, $shopId);
        $this->currentTheme = $themeId;
    }

    private function isThemeConfiguredFromSource(string $themeId, int $shopId, string $themePath): bool
    {
        $themeConfigurationDao = $this->get(ThemeConfigurationDaoInterface::class);
        if (!$themeConfigurationDao->exists($themeId, $shopId)) {
            return false;
        }

        $context = $this->get(BasicContextInterface::class);
        $expectedSource = Path::makeRelative(realpath($themePath), $context->getShopRootPath());

        return $themeConfigurationDao->get($themeId, $shopId)->getSource() === $expectedSource;
    }

    public function setFixtureBaseLanguage(int $languageId): void
    {
        Registry::getLang()->setBaseLanguage($languageId);
    }

    private function getFixturesDirectory(): string
    {
        return "$this->fixtureRoot/Fixtures";
    }

    private function installModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getPackageFixture($moduleId));
    }

    private function activateModuleFixture(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
    }

    private function getPackageFixture(string $moduleId): OxidEshopPackage
    {
        return new OxidEshopPackage("{$this->getFixturesDirectory()}/$moduleId/");
    }

    private function destroyTestContainer(): void
    {
        $this->container = null;
    }
}
