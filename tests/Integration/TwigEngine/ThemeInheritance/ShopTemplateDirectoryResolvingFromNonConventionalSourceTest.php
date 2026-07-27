<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\ThemeInheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ShopTemplateDirectoryResolvingFromNonConventionalSourceTest extends TestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const PARENT_THEME = 'vendorParentTheme';
    private const CHILD_THEME = 'vendorChildTheme';

    protected function setUp(): void
    {
        parent::setUp();

        $this->installTheme(self::PARENT_THEME);
        $this->installTheme(self::CHILD_THEME);
        $this->get(ThemeActivationServiceInterface::class)->activate(self::CHILD_THEME, self::SHOP_ID);
    }

    public function testRenderWithParentTemplateWhenThemeSourceLivesOutsideApplicationViews(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render('template-in-parent-theme.html.twig');

        $this->assertStringContainsString('<parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildTemplateWhenThemeSourceLivesOutsideApplicationViews(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render('template-in-child-theme.html.twig');

        $this->assertStringContainsString('<child-theme-template-contents>', $actual);
    }

    private function installTheme(string $themeId): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install(__DIR__ . "/Fixtures/vendor/$themeId");
    }
}
