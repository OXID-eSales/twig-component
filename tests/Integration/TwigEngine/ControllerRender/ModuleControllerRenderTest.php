<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\ControllerRender;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

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

        $this->initFixtures(__DIR__);
        $this->setupModuleFixture('module1');
        $this->setThemeFixture(self::THEME);
        $this->installThemeConfiguration();
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

        parent::tearDown();
    }

    private function installThemeConfiguration(): void
    {
        $settings = [
            ['sDefaultListDisplayType', 'str', 'infogrid'],
            ['aNrofCatArticles', 'arr', ['10']],
            ['aNrofCatArticlesInGrid', 'arr', ['10']],
            ['blShowBirthdayFields', 'bool', true],
            ['bl_showCompareList', 'bool', true],
            ['bl_showGiftWrapping', 'bool', true],
            ['bl_showVouchers', 'bool', true],
            ['bl_showWishlist', 'bool', true],
            ['iNewBasketItemMessage', 'select', '0'],
        ];

        $configuration = (new ThemeConfiguration())->setId(self::THEME)->setActivated(true);
        foreach ($settings as [$name, $type, $value]) {
            $configuration->addThemeSetting((new Setting())->setName($name)->setType($type)->setValue($value));
        }

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $this->shopID);
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
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_URI'] = '';
    }

    private function switchDebugMode(bool $enable): void
    {
        $this->createContainer();
        $this->container->setParameter(
            'oxid_esales.shop_source_directory',
            "{$this->getFixturesDirectory()}/shop/source/"
        );
        $this->container->setParameter('oxid_esales.debug_mode', $enable);
        $this->compileContainer();
        $this->replaceContainerInstance();

        $this->setThemeFixture($this->currentTheme);
    }

    private function autoloadFixtures(): void
    {
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleController.php';
        require_once __DIR__ . '/Fixtures/module1/src/Contoller/ModuleControllerMissingTemplate.php';
    }
}
