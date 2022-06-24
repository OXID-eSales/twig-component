<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine;

use org\bovigo\vfs\vfsStream;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache\ShopConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/** @runTestsInSeparateProcesses */
final class ModulesTemplateChainSortingTest extends TestCase
{
    use ContainerTrait;

    private const FIXTURE_MODULE_NAMES = [
        'module1',
        'module2',
        'module3',
        'module4',
    ];
    private const FIXTURE_THEME = 'testTheme';
    private const FIXTURE_TEMPLATE_WITH_EXTENDS = 'template-with-extends.html.twig';

    private BasicContextInterface $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->get(BasicContextInterface::class);
        $this->updateShopConfig();
        $this->setUpModules();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    /**
     * @dataProvider testRenderShopTemplateDataProvider
     */
    public function testRenderWithShopTemplateAndSorting(string $sorting, string $expectedResult): void
    {
        $template = self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function testRenderShopTemplateDataProvider(): array
    {
        return [
            [
                '  templateExtensions:
    "%s":
      - module1
      - module2
      - module3
      - module4
',
                '<shop-header><shop-content>'
                . '<module-4-content-ext-shop-default-theme>'
                . '<module-3-content-ext-shop-test-theme>'
                . '<module-2-content-ext-shop-test-theme>'
                . '<module-1-content-ext-shop-test-theme>'
            ],
            [
                '  templateExtensions:
    "%s":
      - module2
      - module4
      - module1
      - module3
',
                '<shop-header><shop-content>'
                . '<module-3-content-ext-shop-test-theme>'
                . '<module-1-content-ext-shop-test-theme>'
                . '<module-4-content-ext-shop-default-theme>'
                . '<module-2-content-ext-shop-test-theme>'
            ],
            [
                '  templateExtensions:
    %s:
      - module2
      - module4
',
                '<shop-header><shop-content>'
                . '<module-3-content-ext-shop-test-theme>'
                . '<module-1-content-ext-shop-test-theme>'
                . '<module-4-content-ext-shop-default-theme>'
                . '<module-2-content-ext-shop-test-theme>'
            ],
        ];
    }

    /**
     * @dataProvider testRenderModuleTemplateDataProvider
     */
    public function testRenderWithModuleTemplateAndSorting(string $sorting, string $expectedResult): void
    {
        $template = '@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function testRenderModuleTemplateDataProvider(): array
    {
        return [
            [
                '  templateExtensions:
    "%s":
      - module2
      - module3
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
            ],
            [
                '  templateExtensions:
    "%s":
      - module3
      - module2
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
            ],
            [
                '  templateExtensions:
    "%s":
      - module2
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
            ],
        ];
    }

    /**
     * @dataProvider testRenderWithShopTemplateAndFaultySortingConfigDataProvider
     */
    public function testRenderWithShopTemplateAmdFaultySortingConfiguration(
        string $sorting,
        string $expectedResult
    ): void {
        $template = self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function testRenderWithShopTemplateAndFaultySortingConfigDataProvider(): array
    {
        return [
            [
                '  templateExtensions:
    %s:
      - module2
      - module2
      - module4
',
                '<shop-header><shop-content>'
                . '<module-3-content-ext-shop-test-theme>'
                . '<module-1-content-ext-shop-test-theme>'
                . '<module-4-content-ext-shop-default-theme>'
                . '<module-2-content-ext-shop-test-theme>'
            ],
            [
                '  templateExtensions:
    %s:
      - module_is_not_installed_1
      - module2
      - module_is_not_installed_1
      - module4
      - module_is_not_installed_2
',
                '<shop-header><shop-content>'
                . '<module-3-content-ext-shop-test-theme>'
                . '<module-1-content-ext-shop-test-theme>'
                . '<module-4-content-ext-shop-default-theme>'
                . '<module-2-content-ext-shop-test-theme>'
            ],
        ];
    }

    /**
     * @dataProvider testRenderWithModuleTemplateAndFaultySortingConfigDataProvider
     */
    public function testRenderWithModuleTemplateAndFaultySortingConfiguration(
        string $sorting,
        string $expectedResult
    ): void {
        $template = '@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function testRenderWithModuleTemplateAndFaultySortingConfigDataProvider(): array
    {
        return [
            [
                '  templateExtensions:
    "%s":
      - module1
      - module2
      - module3
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
            ],
            [
                '  templateExtensions:
    "%s":
      - module2
      - module3
      - module1
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
            ],
            [
                '  templateExtensions:
    "%s":
      - module3
      - module2
      - module_is_not_installed_1
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-content-ext-module-1>'
                . '<module-2-content-ext-module-1>'
                . '<module-3-content-ext-module-1>'
            ],
        ];
    }

    private function setUpModules(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->installModuleFixture('module3');
        $this->installModuleFixture('module4');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->activateModule('module3');
        $this->activateModule('module4');
    }

    private function addTemplateExtensionsSortingToShopConfiguration(string $sorting, string $template): void
    {
        $shopConfiguration = file_get_contents(
            vfsStream::url('configuration/shops/' . $this->context->getDefaultShopId() . '.yaml')
        );

        $templateExtensions = sprintf(
            $sorting,
            str_replace('-', '_', $template)
        );
        $shopConfiguration .= $templateExtensions;
        file_put_contents(
            vfsStream::url('configuration/shops/' . $this->context->getDefaultShopId() . '.yaml'),
            $shopConfiguration
        );
        $this->get(ShopConfigurationCacheInterface::class)->evict($this->context->getDefaultShopId());
    }

    private function installModuleFixture(string $moduleName): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage($moduleName));
    }

    private function getTestPackage(string $moduleName): OxidEshopPackage
    {
        $packageFixturePath = __DIR__ . "/Fixtures/$moduleName/";
        return new OxidEshopPackage($packageFixturePath);
    }

    private function activateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->activate($moduleId, $this->context->getDefaultShopId());
    }

    private function updateShopConfig(): void
    {
        $mockedShopPath = __DIR__ . '/Fixtures/shop/source/';
        Registry::getConfig()->setConfigParam('sShopDir', $mockedShopPath);
        Registry::getConfig()->setConfigParam('sTheme', self::FIXTURE_THEME);
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
