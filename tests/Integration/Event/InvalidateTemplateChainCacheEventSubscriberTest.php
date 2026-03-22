<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class InvalidateTemplateChainCacheEventSubscriberTest extends IntegrationTestCase
{
    private const CACHE_KEY = 'test_template_chain_cache_entry';

    private TagAwareCacheInterface $cache;
    private EventDispatcherInterface $eventDispatcher;
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->get(TagAwareCacheInterface::class);
        $this->eventDispatcher = $this->get(EventDispatcherInterface::class);
        $this->shopId = $this->get(BasicContextInterface::class)->getDefaultShopId();
        $this->cache->invalidateTags(['oxid_esales.cache.twig.template_chain']);
    }

    public function testModuleConfigurationChangedEventInvalidatesTemplateChainCache(): void
    {
        $this->putTaggedCacheEntry();

        $moduleConfiguration = (new ModuleConfiguration())
            ->setId('test_module')
            ->setModuleSource('source/modules/test_module');

        $this->eventDispatcher->dispatch(
            new ModuleConfigurationChangedEvent($moduleConfiguration, $this->shopId)
        );

        $this->assertFalse($this->cache->getItem(self::CACHE_KEY)->isHit());
    }

    public function testThemeSettingChangedEventInvalidatesTemplateChainCache(): void
    {
        $this->putTaggedCacheEntry();

        $this->eventDispatcher->dispatch(
            new ThemeSettingChangedEvent('sTheme', $this->shopId, 'apex')
        );

        $this->assertFalse($this->cache->getItem(self::CACHE_KEY)->isHit());
    }

    public function testThemeActivatedEventInvalidatesTemplateChainCache(): void
    {
        $this->putTaggedCacheEntry();

        $this->eventDispatcher->dispatch(
            new ThemeActivatedEvent($this->shopId, 'test-theme')
        );

        $this->assertFalse($this->cache->getItem(self::CACHE_KEY)->isHit());
    }

    public function testFinalizingModuleDeactivationEventInvalidatesTemplateChainCache(): void
    {
        $this->putTaggedCacheEntry();

        $this->eventDispatcher->dispatch(
            new FinalizingModuleDeactivationEvent($this->shopId, 'test_module')
        );

        $this->assertFalse($this->cache->getItem(self::CACHE_KEY)->isHit());
    }

    private function putTaggedCacheEntry(): void
    {
        $item = $this->cache->getItem(self::CACHE_KEY);
        $item->set('cached_value');
        $item->tag(['oxid_esales.cache.twig.template_chain']);

        $this->cache->save($item);
    }
}
