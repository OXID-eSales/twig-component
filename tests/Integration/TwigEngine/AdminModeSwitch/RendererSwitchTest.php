<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\AdminModeSwitch;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\TestCase;

final class RendererSwitchTest extends TestCase
{
    use TestingFixturesTrait;

    private string $templateInShopAndAdmin = 'template.html.twig';
    private string $templateInShopOnly = 'template_in_shop_only.html.twig';

    public function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setShopSourceFixture();
        $this->setThemeFixture('testTheme');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Registry::getConfig()->setAdminMode(false);
    }

    public function testRenderWithAdminModeSwitchWillSwitchToAppropriateTemplate(): void
    {
        Registry::getConfig()->setAdminMode(true);
        $output = $this->getRenderer()->renderTemplate($this->templateInShopAndAdmin);
        $this->assertStringContainsString('This is an admin area template', $output);

        Registry::getConfig()->setAdminMode(false);
        $output = $this->getRenderer()->renderTemplate($this->templateInShopAndAdmin);
        $this->assertStringContainsString('This is a shop area template', $output);
    }

    public function testRenderWithAdminModeSwitchWillNotLoadTemplateForInactiveMode(): void
    {
        Registry::getConfig()->setAdminMode(false);
        $this->getRenderer()->renderTemplate($this->templateInShopOnly);

        $this->expectException(TemplateNotInChainException::class);
        Registry::getConfig()->setAdminMode(true);
        $this->getRenderer()->renderTemplate($this->templateInShopOnly);
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }
}
