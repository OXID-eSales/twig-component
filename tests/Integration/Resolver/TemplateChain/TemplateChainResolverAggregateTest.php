<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Resolver\TemplateChain;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

class TemplateChainResolverAggregateTest extends TestCase
{
    use ContainerTrait;

    private const MODULE_ID = 'module1';
    private const FIXTURE_MODULE_NAMES = [
        self::MODULE_ID,
    ];
    private const FIXTURE_THEME = 'testTheme';
    private const FIXTURE_TEMPLATE_SHOP = 'template-shop.html.twig';
    private const FIXTURE_TEMPLATE_SHOP_MODULE = 'template-shop-module.html.twig';
    private const FIXTURE_TEMPLATE_MODULE = 'template-module.html.twig';
    private const MAIN_NAMESPACE = '@' . FilesystemLoader::MAIN_NAMESPACE . '/';
    private const MODULE_NAMESPACE = '@' . self::MODULE_ID . '/';
    /** @var BasicContext */
    private $context;

    /** @var array */
    private $testPackageNames = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new BasicContext();
        $this->generateUniquePackageNames();
        $this->updateShopConfig();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();
        parent::tearDown();
    }

    public function testGetChainWithShopTemplate(): void
    {
        $expected = [
            self::MAIN_NAMESPACE . self::FIXTURE_TEMPLATE_SHOP,
        ];

        $actual = $this->get(TemplateChainInterface::class)->getChain(self::FIXTURE_TEMPLATE_SHOP);

        $this->assertSame($expected, $actual);
    }

    public function testGetChainWithModuleTemplate(): void
    {
        $expected = [
            self::MODULE_NAMESPACE . self::FIXTURE_TEMPLATE_MODULE,
        ];
        $this->installModuleFixture(self::MODULE_ID);
        $this->activateModule(self::MODULE_ID);

        $actual = $this->get(TemplateChainInterface::class)->getChain(self::FIXTURE_TEMPLATE_MODULE);

        $this->assertSame($expected, $actual);
    }

    public function testGetChainWithModuleTemplateAndDeactivatedModule(): void
    {
        $this->installModuleFixture(self::MODULE_ID);
        $this->activateModule(self::MODULE_ID);
        $this->deactivateModule(self::MODULE_ID);

        $actual = $this->get(TemplateChainInterface::class)->getChain(self::FIXTURE_TEMPLATE_MODULE);

        $this->assertEmpty($actual);
    }

    public function testGetChainWithShopModuleTemplateAndActiveModule(): void
    {
        $expected = [
            self::MODULE_NAMESPACE . self::FIXTURE_TEMPLATE_SHOP_MODULE,
            self::MAIN_NAMESPACE . self::FIXTURE_TEMPLATE_SHOP_MODULE,
        ];
        $this->installModuleFixture(self::MODULE_ID);
        $this->activateModule(self::MODULE_ID);

        $actual = $this->get(TemplateChainInterface::class)->getChain(self::FIXTURE_TEMPLATE_SHOP_MODULE);

        $this->assertSame($expected, $actual);
    }

    public function testGetChainWithShopModuleTemplateAndDeactivatedModule(): void
    {
        $expected = [
            self::MAIN_NAMESPACE . self::FIXTURE_TEMPLATE_SHOP_MODULE,
        ];
        $this->installModuleFixture(self::MODULE_ID);
        $this->activateModule(self::MODULE_ID);
        $this->deactivateModule(self::MODULE_ID);

        $actual = $this->get(TemplateChainInterface::class)->getChain(self::FIXTURE_TEMPLATE_SHOP_MODULE);

        $this->assertSame($expected, $actual);
    }

    private function installModuleFixture(string $moduleName): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage($moduleName));
    }

    private function getTestPackage(string $moduleName): OxidEshopPackage
    {
        $packageFixturePath = __DIR__ . "/Fixtures/templateChainResolverAggregateTest/$moduleName/";
        return new OxidEshopPackage($this->testPackageNames[$moduleName], $packageFixturePath);
    }

    private function activateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->activate($moduleId, $this->context->getDefaultShopId());
    }

    private function deactivateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate($moduleId, $this->context->getDefaultShopId());
    }

    private function updateShopConfig(): void
    {
        $mockedShopPath = __DIR__ . '/Fixtures/templateChainResolverAggregateTest/shop/source/';
        Registry::getConfig()->setConfigParam('sShopDir', $mockedShopPath);
        Registry::getConfig()->setConfigParam('sTheme', self::FIXTURE_THEME);
    }

    private function generateUniquePackageNames(): void
    {
        foreach (self::FIXTURE_MODULE_NAMES as $moduleName) {
            $this->testPackageNames[$moduleName] = uniqid('package_', true);
        }
    }

    private function cleanUpTestData(): void
    {
        foreach (self::FIXTURE_MODULE_NAMES as $moduleName) {
            $this->uninstallModuleFixture($moduleName);
        }
    }

    private function uninstallModuleFixture(string $moduleName): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage($moduleName));
    }
}