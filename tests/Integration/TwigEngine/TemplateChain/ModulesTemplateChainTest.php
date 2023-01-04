<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

/** @runTestsInSeparateProcesses */
final class ModulesTemplateChainTest extends TestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

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

        $this->initFixtures(__DIR__);
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);
    }

    protected function tearDown(): void
    {
        foreach (self::MODULE_IDS as $moduleId) {
            $this->uninstallModuleFixture($moduleId);
        }

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
        $this->setupModuleFixture('module1');

        $actual = $this->get(TemplateEngineInterface::class)->render('@module1/' . self::TEMPLATE_NO_EXTENDS);

        $this->assertStringContainsString('<module1-template-no-extends>', $actual);
    }

    public function testRenderWithActiveModuleExtendingShop(): void
    {
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-test-theme>', $actual);
    }

    public function testRenderWithActiveModuleExtendingShopAndNonHtmlTemplate(): void
    {
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_NON_HTML_FILE);

        $this->assertStringContainsString('<shop-content>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-default-theme>', $actual);
    }

    public function testRenderWithActiveModuleAndMissingThemeTemplateWillUseTemplateFromDefaultTheme(): void
    {
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME_2);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content-theme-2>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-default-theme>', $actual);
    }

    public function testRenderWithInactiveModule(): void
    {
        $this->setupModuleFixture('module1');
        $this->deactivateModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringNotContainsString('<module-1-extending-shop-test-theme>', $actual);
    }

    public function testRenderWith2ActiveModules(): void
    {
        $this->setupModuleFixture('module1');
        $this->setupModuleFixture('module2');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-2-extending-shop-test-theme>', $actual);
    }

    public function testRenderWith3ActiveModules(): void
    {
        $this->setupModuleFixture('module1');
        $this->setupModuleFixture('module2');
        $this->setupModuleFixture('module3');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-2-extending-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-3-extending-shop-test-theme>', $actual);
    }

    public function testRenderWith3ModulesAndDeactivation(): void
    {
        $this->setupModuleFixture('module1');
        $this->setupModuleFixture('module2');
        $this->setupModuleFixture('module3');
        $this->deactivateModuleFixture('module2');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_EXTENDS);

        $this->assertStringContainsString('<shop-header><shop-content>', $actual);
        $this->assertStringContainsString('<module-1-extending-shop-test-theme>', $actual);
        $this->assertStringNotContainsString('<module-2-extending-shop-test-theme>', $actual);
        $this->assertStringContainsString('<module-3-extending-shop-test-theme>', $actual);
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
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_INCLUDE);

        $this->assertStringContainsString(
            "<shop-header><module-1-included-extending-shop-test-theme>\n<shop-content-test-theme>",
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
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);

        $this->expectException(LoaderError::class);

        $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_WITH_INVALID_EXTENDS);
    }
}
