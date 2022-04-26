<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Twig\Error\SyntaxError;

/** @runTestsInSeparateProcesses */
final class ModulesTemplateChainTest extends TestCase
{
    use ContainerTrait;

    private const FIXTURE_MODULE_NAMES = [
        'module1',
        'module2',
        'module3',
    ];
    private const FIXTURE_THEME = 'testTheme';
    private const FIXTURE_TEMPLATE_WITH_EXTENDS = 'template-with-extends.html.twig';
    private const FIXTURE_TEMPLATE_WITH_INVALID_EXTENDS = 'template-with-invalid-extends.html.twig';
    private const FIXTURE_TEMPLATE_WITH_CONDITIONAL_EXTENDS = 'template-with-conditional-extends.html.twig';
    private const FIXTURE_TEMPLATE_WITH_ARRAY_EXTENDS = 'template-with-array-extends.html.twig';
    private const FIXTURE_TEMPLATE_WITH_INCLUDE = 'template-with-include.html.twig';

    private BasicContext $context;

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

    public function testEngineRenderWithActiveModule(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content>', $actual);
    }

    public function testEngineRenderWithInactiveModule(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->deactivateModule('module1');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringNotContainsString('<module-1-content>', $actual);
    }

    public function testEngineRenderWith2ActiveModules(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->activateModule('module1');
        $this->activateModule('module2');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content>', $actual);
        $this->assertStringContainsString('<module-2-content>', $actual);
    }

    public function testEngineRenderWith3ActiveModules(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->installModuleFixture('module3');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->activateModule('module3');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content>', $actual);
        $this->assertStringContainsString('<module-2-content>', $actual);
        $this->assertStringContainsString('<module-3-content>', $actual);
    }

    public function testEngineRenderWith3ModulesAndDeactivation(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->installModuleFixture('module3');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->activateModule('module3');
        $this->deactivateModule('module2');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content>', $actual);
        $this->assertStringNotContainsString('<module-2-content>', $actual);
        $this->assertStringContainsString('<module-3-content>', $actual);
    }

    public function testEngineRenderWithConditionalExtends(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::FIXTURE_TEMPLATE_WITH_CONDITIONAL_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<template_with_conditional_extends-content>', $actual);
    }

    public function testEngineRenderWithArrayExtends(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::FIXTURE_TEMPLATE_WITH_ARRAY_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<template_with_array_extends-content>', $actual);
    }

    public function testEngineRenderWithIncludeAndMultipleModulesWillRenderLastInChain(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $actual = $this->get(TemplateEngineInterface::class)->render(self::FIXTURE_TEMPLATE_WITH_INCLUDE);

        $this->assertStringContainsString('<shop-header><module-1-included-content><shop-content>', $actual);
    }

    public function testEngineRenderWithInvalidExtendsValue(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $this->expectException(SyntaxError::class);

        $this->get(TemplateEngineInterface::class)->render('@module1/' . self::FIXTURE_TEMPLATE_WITH_INVALID_EXTENDS);
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

    private function deactivateModule(string $moduleId): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate($moduleId, $this->context->getDefaultShopId());
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
