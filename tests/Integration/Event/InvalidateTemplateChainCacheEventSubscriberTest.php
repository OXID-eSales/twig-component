<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheInterface;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheNotFoundException;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InvalidateTemplateChainCacheEventSubscriberTest extends TestCase
{
    use ContainerTrait;

    private const TEMPLATE_NAME = 'template.html.twig';

    private TemplateChainCacheInterface $cache;
    private EventDispatcherInterface $eventDispatcher;
    private int $shopId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->get(TemplateChainCacheInterface::class);
        $this->eventDispatcher = $this->get(EventDispatcherInterface::class);
        $this->shopId = $this->get(BasicContextInterface::class)->getDefaultShopId();
    }

    protected function tearDown(): void
    {
        $this->cache->invalidate($this->shopId);

        parent::tearDown();
    }

    public function testModuleConfigurationChangedEventInvalidatesTemplateChainCache(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('test-module');
        $this->putCacheEntry();

        $this->eventDispatcher->dispatch(
            new ModuleConfigurationChangedEvent($moduleConfiguration, $this->shopId)
        );

        $this->expectException(TemplateChainCacheNotFoundException::class);

        $this->cache->get(self::TEMPLATE_NAME);
    }

    public function testThemeSettingChangedEventInvalidatesTemplateChainCache(): void
    {
        $this->putCacheEntry();

        $this->eventDispatcher->dispatch(
            new ThemeSettingChangedEvent('sTheme', $this->shopId, 'apex')
        );

        $this->expectException(TemplateChainCacheNotFoundException::class);

        $this->cache->get(self::TEMPLATE_NAME);
    }

    public function testThemeActivatedEventInvalidatesTemplateChainCache(): void
    {
        $this->putCacheEntry();

        $this->eventDispatcher->dispatch(
            new ThemeActivatedEvent($this->shopId, 'test-theme')
        );

        $this->expectException(TemplateChainCacheNotFoundException::class);

        $this->cache->get(self::TEMPLATE_NAME);
    }

    private function putCacheEntry(): void
    {
        $this->cache->put(
            self::TEMPLATE_NAME,
            $this->createTemplateChain()
        );
    }

    private function createTemplateChain(): TemplateChain
    {
        $templateChain = new TemplateChain();
        $templateChain->append(new ShopTemplateType(self::TEMPLATE_NAME));

        return $templateChain;
    }
}
