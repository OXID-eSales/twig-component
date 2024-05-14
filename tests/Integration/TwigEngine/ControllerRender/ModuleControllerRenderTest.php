<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\ControllerRender;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

#[RunTestsInSeparateProcesses]
final class ModuleControllerRenderTest extends TestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;
    use ProphecyTrait;

    private const MODULE_IDS = [
        'module1',
    ];
    private const THEME = 'testTheme';

    private int $shopID = 1;
	private ShopControl $shopControl;

	protected function setUp(): void
    {
        parent::setUp();

	    $_GET['searchparam'] = '';
	    $_GET['page'] = '';
	    $_GET['tpl'] = '';

        $this->initFixtures(__DIR__);
        $this->setupModuleFixture('module1');
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);
        $this->setFixtureBaseLanguage(0);
        $this->autoloadFixtures();
        $this->stubRequestData();

        $this->shopControl = new ShopControl();
    }

    protected function tearDown(): void
    {
        foreach (self::MODULE_IDS as $moduleId) {
            $this->uninstallModuleFixture($moduleId);
        }

	    unset($_GET['searchparam']);
	    unset($_GET['page']);
	    unset($_GET['tpl']);

        parent::tearDown();
    }

    public function testRenderWithExistingTemplate(): void
    {
        ob_start();
	    $this->shopControl->start('module1_controller', '');
	    $output = ob_get_clean();

	    $this->assertStringContainsString('Module 1 Header', $output);
        $this->assertStringContainsString((new \DateTime())->format('Y-m-d'), $output);
    }

    public function testRenderWithMissingTemplateWillPassTranslatedMessageToExceptionTemplate(): void
    {
        $this->switchDebugMode(true);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template', '');
        $output = ob_get_clean();

        $this->assertStringContainsString(
            \htmlspecialchars('Template "@module1/module_controller_missing_template" nicht gefunden'),
            $output
        );
    }

    public function testRenderWithMissingTemplateAndDebugOff(): void
    {
        $this->switchDebugMode(false);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template', '');
        $output = ob_get_clean();

        $this->assertStringNotContainsString(
            \htmlspecialchars('Template "@module1/module_controller_missing_template" nicht gefunden'),
            $output
        );
    }

    public function testRenderWithMissingTemplateWillLogMessage(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        Registry::set('logger', $logger->reveal());
        $this->switchDebugMode(true);

        ob_start();
        $this->shopControl->start('module1_controller_missing_template', '');
        ob_get_clean();

        $logger->error(
            Argument::containingString('module_controller_missing_template'),
            Argument::any()
        )->shouldHaveBeenCalled();
    }

    private function stubRequestData(): void
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['SCRIPT_URI'] = '';
    }

    private function switchDebugMode(bool $enable): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_debug_mode', $enable);
        $this->container->compile();
        $this->attachContainerToContainerFactory();
    }

    private function autoloadFixtures(): void
    {
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleController.php';
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleControllerMissingTemplate.php';
    }
}
