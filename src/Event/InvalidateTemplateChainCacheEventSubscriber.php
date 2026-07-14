<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ModuleSetupEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class InvalidateTemplateChainCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TagAwareCacheInterface $cache)
    {
    }

    public function invalidateTemplateChainCache(
        ModuleSetupEvent|ModuleConfigurationChangedEvent|ThemeConfigurationChangedEvent|ThemeActivatedEvent $event
    ): void {
        $this->cache->invalidateTags(['oxid_esales.cache.twig.template_chain']);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::class => 'invalidateTemplateChainCache',
            FinalizingModuleDeactivationEvent::class => 'invalidateTemplateChainCache',
            ModuleConfigurationChangedEvent::class => 'invalidateTemplateChainCache',
            ThemeConfigurationChangedEvent::class => 'invalidateTemplateChainCache',
            ThemeActivatedEvent::class => 'invalidateTemplateChainCache',
        ];
    }
}
