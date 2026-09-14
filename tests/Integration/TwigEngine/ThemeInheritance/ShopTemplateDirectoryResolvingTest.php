<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\ThemeInheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\ShopTemplateDirectoryResolver;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use Twig\Loader\FilesystemLoader;

final class ShopTemplateDirectoryResolvingTest extends IntegrationTestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const PARENT_THEME = 'parentTheme';
    private const CHILD_THEME = 'childTheme';
    private const CHILD_THEME_WITHOUT_TEMPLATES = 'childThemeWithoutTemplates';
    private const PARENT_THEME_TEMPLATE_DIRECTORY = '/Fixtures/shop/source/Application/views/parentTheme/tpl/';
    private const TEMPLATE_IN_PARENT_THEME = 'template-in-parent-theme.html.twig';
    private const TEMPLATE_IN_CHILD_THEME = 'template-in-child-theme.html.twig';
    private const TEMPLATE_IN_BOTH_THEMES = 'template-in-both-themes.html.twig';

    public function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setThemeFixture(self::PARENT_THEME);
        $this->reloadTestContainer();
    }

    public function testRenderWithParentThemeAndParentTemplate(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_PARENT_THEME);

        $this->assertStringContainsString('<parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildThemeAndParentTemplate(): void
    {
        $this->setThemeFixture(self::CHILD_THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_PARENT_THEME);

        $this->assertStringContainsString('<parent-theme-template-contents>', $actual);
    }

    public function testRenderWithChildThemeAndChildTemplate(): void
    {
        $this->setThemeFixture(self::CHILD_THEME);

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
        $this->setThemeFixture(self::CHILD_THEME);

        $actual = $this->get(TemplateEngineInterface::class)->render(self::TEMPLATE_IN_BOTH_THEMES);

        $this->assertStringContainsString('<both-themes-template-child-theme-template-contents>', $actual);
    }

    public function testChildThemeWithoutTemplatesResolvesParentThemeDirectoryOnce(): void
    {
        $this->setThemeFixture(self::CHILD_THEME_WITHOUT_TEMPLATES);

        $directories = $this->get(ShopTemplateDirectoryResolver::class)->getTemplateDirectories();

        $this->assertEquals(
            [new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, __DIR__ . self::PARENT_THEME_TEMPLATE_DIRECTORY)],
            $directories
        );
    }
}
