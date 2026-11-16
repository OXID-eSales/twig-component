<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\AdminUi;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;

final class AdminUiTemplateRenderTest extends IntegrationTestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const MODULE_ID = 'adminUiModule';

    public function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setupModuleFixture(self::MODULE_ID);

        $directoryResolver = $this->createStub(AdminUiDirectoryResolverInterface::class);
        $directoryResolver->method('getDirectories')->willReturn([
            __DIR__ . '/Fixtures/adminUiOverlay',
            __DIR__ . '/Fixtures/adminUi',
        ]);
        $this->createContainer();
        $this->replaceService(AdminUiDirectoryResolverInterface::class, $directoryResolver);
        $this->replaceContainerInstance();

        Registry::getConfig()->setAdminMode(true);
    }

    public function tearDown(): void
    {
        $this->uninstallModuleFixture(self::MODULE_ID);

        parent::tearDown();
    }

    public function testRenderWithAdminUiTemplate(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render('admin-ui-template.html.twig');

        $this->assertStringContainsString('<admin-ui-template>', $actual);
    }

    public function testRenderPrefersTemplateOfPrecedingDirectory(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render('overridden-template.html.twig');

        $this->assertStringContainsString('<overlay-template>', $actual);
    }

    public function testRenderAppliesModuleExtensionForAdminUi(): void
    {
        $actual = $this->get(TemplateEngineInterface::class)->render('extendable-template.html.twig');

        $this->assertStringContainsString('<admin-ui-template><admin-ui-module-extension>', $actual);
    }
}
