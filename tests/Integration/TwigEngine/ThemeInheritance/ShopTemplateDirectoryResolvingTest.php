<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\ThemeInheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
final class ShopTemplateDirectoryResolvingTest extends TestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const PARENT_THEME = 'parentTheme';
    private const CHILD_THEME = 'childTheme';
    private const TEMPLATE_IN_PARENT_THEME = 'template-in-parent-theme.html.twig';
    private const TEMPLATE_IN_CHILD_THEME = 'template-in-child-theme.html.twig';
    private const TEMPLATE_IN_BOTH_THEMES = 'template-in-both-themes.html.twig';

    protected function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::PARENT_THEME);
    }

    public function testRenderWithParentThemeAndParentTemplate(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_PARENT_THEME);

        $this->assertStringContainsString('<parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildThemeAndParentTemplate(): void
    {
        $this->setChildThemeFixture(self::CHILD_THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_PARENT_THEME);

        $this->assertStringContainsString('<parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildThemeAndChildTemplate(): void
    {
        $this->setChildThemeFixture(self::CHILD_THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_CHILD_THEME);

        $this->assertStringContainsString('<child-theme-template-contents>', $actual);
    }

    public function testRenderWithParentThemeAndSharedTemplate(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_BOTH_THEMES);

        $this->assertStringContainsString('<both-themes-template-parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildThemeAndSharedTemplate(): void
    {
        $this->setChildThemeFixture(self::CHILD_THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_BOTH_THEMES);

        $this->assertStringContainsString('<both-themes-template-child-theme-template-contents>', $actual);
    }
}
