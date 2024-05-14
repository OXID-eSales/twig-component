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
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;

trait TestingFixturesTrait
{
    use ContainerTrait;

    private string $fixtureRoot = __DIR__;

    public function initFixtures(string $fixtureRoot): void
    {
        $this->fixtureRoot = $fixtureRoot;
        Registry::getConfig()->reinitialize();
    }

    public function setupModuleFixture(string $moduleId): void
    {
        $this->installModuleFixture($moduleId);
        $this->activateModuleFixture($moduleId);
    }

    public function uninstallModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getPackageFixture($moduleId));
    }

    public function deactivateModuleFixture(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
    }

    public function setShopSourceFixture(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_shop_source_directory', "{$this->getFixturesDirectory()}/shop/source/");
        $this->compileContainer();
        $this->attachContainerToContainerFactory();
    }

    public function setThemeFixture(string $themeId): void
    {
        Registry::getConfig()->setConfigParam('sTheme', $themeId);
    }

    public function setChildThemeFixture(string $themeId): void
    {
        Registry::getConfig()->setConfigParam('sCustomTheme', $themeId);
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
}
