<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Event\AdminModeSwitch;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Event\AdminModeChangedEvent;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Twig\Tests\Integration\Event\AdminModeSwitch\Fixtures\EmailStub;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RendererSwitchTest extends IntegrationTestCase
{
    use TestingFixturesTrait;

    private int $eventFiredCount = 0;

    public function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setShopSourceFixture();
        $this->setThemeFixture('testTheme');
        Registry::getConfig()->setAdminMode(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Registry::getConfig()->setAdminMode(false);
        $this->eventFiredCount = 0;
    }

    public function testSendSentNowMailWillSwitchToAppropriateTemplate(): void
    {
        $email = new EmailStub();

        $email->sendSendedNowMail($this->getOrderStub());

        $this->assertStringContainsString(
            'This is a shop area email template',
            $email->getBody(),
        );
    }

    public function testSendSentNowMailWillSwitchBackToAdminMode(): void
    {
        (new EmailStub())->sendSendedNowMail($this->getOrderStub());

        $this->assertTrue(
            Registry::getConfig()->isAdmin()
        );
    }

    public function testSendSentNowMailWillFireAnEvent(): void
    {
        $this->initEventSpy();

        (new EmailStub())->sendSendedNowMail($this->getOrderStub());

        $this->assertEquals(2, $this->eventFiredCount);
    }

    public function testSendDownloadLinksMailWillSwitchToAppropriateTemplate(): void
    {
        $email = new EmailStub();

        $email->sendDownloadLinksMail($this->getOrderStub());

        $this->assertStringContainsString(
            'This is a shop area email template',
            $email->getBody(),
        );
    }

    public function testSendDownloadLinksMailWillSwitchBackToAdminMode(): void
    {
        (new EmailStub())->sendDownloadLinksMail($this->getOrderStub());

        $this->assertTrue(
            Registry::getConfig()->isAdmin()
        );
    }

    public function testSendDownloadLinksMailWillFireAnEvent(): void
    {
        $this->initEventSpy();

        (new EmailStub())->sendSendedNowMail($this->getOrderStub());

        $this->assertEquals(2, $this->eventFiredCount);
    }

    private function getOrderStub(): Order
    {
        $orderStub = oxNew(Order::class);
        $orderStub->oxorder__oxbillfname = new Field('Some first name');
        $orderStub->oxorder__oxbilllname = new Field('Some last name');
        $orderStub->oxorder__oxbillemail->value = new Field('billing-address@example.com');

        return $orderStub;
    }

    private function initEventSpy(): void
    {
        ContainerFactory::getInstance()
            ->getContainer()
            ->get(EventDispatcherInterface::class)
            ->addListener(
                AdminModeChangedEvent::class,
                function (AdminModeChangedEvent $event) {
                    $this->eventFiredCount++;
                },
                -1000000
            );
    }
}
