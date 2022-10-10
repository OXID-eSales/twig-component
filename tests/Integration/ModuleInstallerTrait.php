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

trait ModuleInstallerTrait
{
    private string $fixtureRoot = __DIR__;

    public function setFixtureRoot(string $fixtureRoot): void
    {
        $this->fixtureRoot = $fixtureRoot;
    }

    public function installModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage($moduleId));
    }

    public function getTestPackage(string $moduleId): OxidEshopPackage
    {
        return new OxidEshopPackage("$this->fixtureRoot/Fixtures/$moduleId/");
    }

    public function activateModule(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
    }

    public function deactivateModule(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
    }

    public function updateShopConfig(string $themeId): void
    {
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('sShopDir', "$this->fixtureRoot/Fixtures/shop/source/");
        Registry::getConfig()->setConfigParam('sTheme', $themeId);
    }

    public function cleanUpTestData(array $moduleIds): void
    {
        foreach ($moduleIds as $moduleId) {
            $this->uninstallModuleFixture($moduleId);
        }
    }

    public function uninstallModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage($moduleId));
    }
}
