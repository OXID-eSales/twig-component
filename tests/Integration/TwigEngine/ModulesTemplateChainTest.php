<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;
use OxidEsales\Twig\Tests\Integration\ModuleInstallerTrait;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

/** @runTestsInSeparateProcesses */
final class ModulesTemplateChainTest extends TestCase
{
    use ContainerTrait;
    use ModuleInstallerTrait;

    private const MODULE_IDS = [
        'module1',
        'module2',
        'module3',
        'module4',
    ];
    private const THEME = 'testTheme';
    private const THEME_2 = 'testTheme2';
    private const TEMPLATE_NO_EXTENDS = 'template-no-extends';
    private const TEMPLATE_WITH_EXTENDS = 'template-with-extends.html.twig';
    private const TEMPLATE_WITH_NON_HTML_FILE = 'template-non-html-with-extends.xml.twig';
    private const TEMPLATE_WITH_INVALID_EXTENDS = 'template-with-invalid-extends.html.twig';
    private const TEMPLATE_WITH_CONDITIONAL_EXTENDS = 'template-with-conditional-extends.html.twig';
    private const TEMPLATE_WITH_ARRAY_EXTENDS = 'template-with-array-extends.html.twig';
    private const TEMPLATE_WITH_INCLUDE = 'template-with-include.html.twig';
    private const TEMPLATE_INCLUDE_NON_TEMPLATE_FILE = 'template-include-non-template-file';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setFixtureRoot(__DIR__);
        $this->updateShopConfig(self::THEME);
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData(self::MODULE_IDS);
        parent::tearDown();
    }

    public function testRenderWithNonExistingTemplate(): void
    {
        $nonExistingTemplate = uniqid('template-', true) . '.html.twig';

        $this->expectException(TemplateNotInChainException::class);

        $this->get(TemplateEngineInterface::class)->render($nonExistingTemplate);
    }

    public function testRenderWithShopsSingeTemplate(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_NO_EXTENDS);

        $this->assertStringContainsString('<shop-template-no-extends>', $actual);
    }

    public function testRenderWithModulesSingeTemplate(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::TEMPLATE_NO_EXTENDS);

        $this->assertStringContainsString('<module1-template-no-extends>', $actual);
    }

    public function testRenderWithActiveModuleExtendingShop(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-test-theme>', $actual);
    }

    public function testRenderWithActiveModuleExtendingShopAndNonHtmlTemplate(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_NON_HTML_FILE);

        $this->assertStringContainsString('<shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-default-theme>', $actual);
    }

    public function testRenderWithActiveModuleAndMissingThemeTemplateWillUseTemplateFromDefaultTheme(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->updateShopConfig(self::THEME_2);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content-theme-2>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-default-theme>', $actual);
    }

    public function testRenderWithInactiveModule(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->deactivateModule('module1');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringNotContainsString('<module-1-content-ext-shop-test-theme>', $actual);
    }

    public function testRenderWith2ActiveModules(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-2-content-ext-shop-test-theme>', $actual);
    }

    public function testRenderWith3ActiveModules(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->installModuleFixture('module3');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->activateModule('module3');
        $this->updateShopConfig(self::THEME);


        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-2-content-ext-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-3-content-ext-shop-test-theme>', $actual);
    }

    public function testRenderWith3ModulesAndDeactivation(): void
    {
        $this->installModuleFixture('module1');
        $this->installModuleFixture('module2');
        $this->installModuleFixture('module3');
        $this->activateModule('module1');
        $this->activateModule('module2');
        $this->activateModule('module3');
        $this->deactivateModule('module2');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-content-ext-shop-test-theme>', $actual);
        $this->assertStringNotContainsString('<module-2-content-ext-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-3-content-ext-shop-test-theme>', $actual);
    }

    public function testRenderWithConditionalExtends(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_CONDITIONAL_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<template-with-conditional-extends-content>', $actual);
    }

    public function testRenderWithArrayExtends(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_ARRAY_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<template-with-array-extends-content>', $actual);
    }

    public function testRenderWithIncludeAndMultipleModulesWillRenderLastInChain(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->updateShopConfig(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_INCLUDE);

        $this->assertStringContainsString(
            '<shop-header><module-1-included-content-ext-shop-test-theme><shop-content-test-theme>',
            $actual
        );
    }

    public function testRenderWithIncludedNonTemplateFileWillRenderWithoutErrors(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_INCLUDE_NON_TEMPLATE_FILE);

        $this->assertStringContainsString('<include-file-html-contents>', $actual);
        $this->assertStringContainsString('<shop-template-include-non-template>', $actual);
    }

    public function testRenderWithInvalidExtendsValue(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->updateShopConfig(self::THEME);

        $this->expectException(LoaderError::class);

        $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_INVALID_EXTENDS);
    }
}
