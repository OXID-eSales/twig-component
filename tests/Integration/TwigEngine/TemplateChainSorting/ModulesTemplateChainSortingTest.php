<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\TemplateChainSorting;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\TestCase;

/** @runTestsInSeparateProcesses */
final class ModulesTemplateChainSortingTest extends TestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const MODULE_IDS = [
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
        $this->initFixtures(__DIR__);
        foreach (self::MODULE_IDS as $moduleId) {
            $this->setupModuleFixture($moduleId);
        }
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::FIXTURE_THEME);
    }

    protected function tearDown(): void
    {
        foreach (self::MODULE_IDS as $moduleId) {
            $this->uninstallModuleFixture($moduleId);
        }

        parent::tearDown();
    }

    /**
     * @dataProvider renderShopTemplateDataProvider
     */
    public function testRenderWithShopTemplateAndSorting(string $sorting, string $expectedResult): void
    {
        $template = self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function renderShopTemplateDataProvider(): array
    {
        return [
            [
                '"%s":
      - module1
      - module2
      - module3
      - module4
',
                '<shop-header><shop-content>'
                . '<module-4-extending-shop>'
                . '<module-3-extending-shop>'
                . '<module-2-extending-shop>'
                . '<module-1-extending-shop>'
            ],
            [
                '"%s":
      - module2
      - module4
      - module1
      - module3
',
                '<shop-header><shop-content>'
                . '<module-3-extending-shop>'
                . '<module-1-extending-shop>'
                . '<module-4-extending-shop>'
                . '<module-2-extending-shop>'
            ],
            [
                '%s:
      - module2
      - module4
',
                '<shop-header><shop-content>'
                . '<module-3-extending-shop>'
                . '<module-1-extending-shop>'
                . '<module-4-extending-shop>'
                . '<module-2-extending-shop>'
            ],
        ];
    }

    /**
     * @dataProvider renderModuleTemplateDataProvider
     */
    public function testRenderWithModuleTemplateAndSorting(string $sorting, string $expectedResult): void
    {
        $template = '@module1/' . self::FIXTURE_TEMPLATE_WITH_EXTENDS;
        $this->addTemplateExtensionsSortingToShopConfiguration($sorting, $template);

        $actual = $this->get(TemplateEngineInterface::class)->render($template);

        $this->assertStringContainsString($expectedResult, $actual);
    }

    public function renderModuleTemplateDataProvider(): array
    {
        return [
            [
                '"%s":
      - module2
      - module3
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-3-extending-module-1>'
                . '<module-2-extending-module-1>'
            ],
            [
                '"%s":
      - module3
      - module2
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-2-extending-module-1>'
                . '<module-3-extending-module-1>'
            ],
            [
                '"%s":
      - module2
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-3-extending-module-1>'
                . '<module-2-extending-module-1>'
            ],
        ];
    }

    /**
     * @dataProvider renderWithShopTemplateAndFaultySortingConfigDataProvider
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

    public function renderWithShopTemplateAndFaultySortingConfigDataProvider(): array
    {
        return [
            [
                '%s:
      - module2
      - module2
      - module4
',
                '<shop-header><shop-content>'
                . '<module-3-extending-shop>'
                . '<module-1-extending-shop>'
                . '<module-4-extending-shop>'
                . '<module-2-extending-shop>'
            ],
            [
                '%s:
      - module_is_not_installed_1
      - module2
      - module_is_not_installed_1
      - module4
      - module_is_not_installed_2
',
                '<shop-header><shop-content>'
                . '<module-3-extending-shop>'
                . '<module-1-extending-shop>'
                . '<module-4-extending-shop>'
                . '<module-2-extending-shop>'
            ],
        ];
    }

    /**
     * @dataProvider renderWithModuleTemplateAndFaultySortingConfigDataProvider
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

    public function renderWithModuleTemplateAndFaultySortingConfigDataProvider(): array
    {
        return [
            [
                '"%s":
      - module1
      - module2
      - module3
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-3-extending-module-1>'
                . '<module-2-extending-module-1>'
            ],
            [
                '"%s":
      - module2
      - module3
      - module1
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-3-extending-module-1>'
                . '<module-2-extending-module-1>'
            ],
            [
                '"%s":
      - module3
      - module2
      - module_is_not_installed_1
      - module4
',
                '<module1-header><module1-content>'
                . '<module-4-extending-module-1>'
                . '<module-2-extending-module-1>'
                . '<module-3-extending-module-1>'
            ],
        ];
    }

    private function addTemplateExtensionsSortingToShopConfiguration(string $sorting, string $template): void
    {
        $templateExtensions = sprintf(
            $sorting,
            $template
        );
        file_put_contents(
            vfsStream::url("configuration/shops/{$this->context->getDefaultShopId()}/template_extension_chain.yaml"),
            $templateExtensions
        );
    }
}
