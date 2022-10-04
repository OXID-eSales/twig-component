<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

final class ModuleControllerRenderTest extends TestCase
{
    use ModuleInstallerTrait;
    use ProphecyTrait;

    private const MODULE_IDS = [
        'module1',
    ];
    private const THEME = 'testTheme';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setFixtureRoot(__DIR__);
        $this->updateShopConfig(self::THEME);
        $this->mockAutoload();

        $this->shopControl = new ShopControl();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData(self::MODULE_IDS);
        parent::tearDown();
    }

    public function testRenderWithExistingTemplate(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');

        ob_start();
        $this->shopControl->start('module1_controller');
        $output = ob_get_clean();

        $this->assertStringContainsString('Module 1 Header', $output);
        $this->assertStringContainsString((new \DateTime())->format('Y-m-d'), $output);
    }

    public function testRenderWithMissingTemplateWillPassTranslatedMessageToExceptionTemplate(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->switchDebugMode(true);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template');
        $output = ob_get_clean();

        $this->assertStringContainsString(
            \htmlspecialchars('Template "@module1/module_controller_missing_template" nicht gefunden'),
            $output
        );
    }

    public function testRenderWithMissingTemplateAndDebugOff(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $this->switchDebugMode(false);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template');
        $output = ob_get_clean();

        $this->assertStringNotContainsString(
            \htmlspecialchars('Template "@module1/module_controller_missing_template" nicht gefunden'),
            $output
        );
    }

    public function testRenderWithMissingTemplateWillLogMessage(): void
    {
        $this->installModuleFixture('module1');
        $this->activateModule('module1');
        $logger = $this->prophesize(LoggerInterface::class);
        Registry::set('logger', $logger->reveal());
        $this->switchDebugMode(true);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template');
        ob_get_clean();

        $logger->error(
            Argument::containingString('module_controller_missing_template'),
            Argument::any()
        )->shouldHaveBeenCalled();
    }

    private function switchDebugMode(bool $enable): void
    {
        $configFile = $this->prophesize(ConfigFile::class);
        $configFile->getVar('sCompileDir')
            ->willReturn(
                $this->get(ContextInterface::class)->getTemplateCacheDirectory()
            );
        $configFile->getVar('iDebug')->willReturn($enable);
        Registry::set(ConfigFile::class, $configFile->reveal());
    }

    private function get(string $serviceId)
    {
        return ContainerFactory::getInstance()->getContainer()->get($serviceId);
    }

    private function mockAutoload(): void
    {
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleController.php';
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleControllerMissingTemplate.php';
    }
}
