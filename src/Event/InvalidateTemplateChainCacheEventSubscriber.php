<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ModuleSetupEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InvalidateTemplateChainCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TemplateChainCacheInterface $templateChainCache)
    {
    }

    public function invalidateForModuleChange(ModuleSetupEvent|ModuleConfigurationChangedEvent $event): void
    {
        $this->invalidate($event->getShopId());
    }

    public function invalidateForThemeChange(ThemeSettingChangedEvent|ThemeActivatedEvent $event): void
    {
        $this->invalidate($event->getShopId());
    }

    public function invalidateForShopCacheClear(ClearShopCacheEvent $event): void
    {
        $this->invalidate($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::class   => 'invalidateForModuleChange',
            FinalizingModuleDeactivationEvent::class => 'invalidateForModuleChange',
            ModuleConfigurationChangedEvent::class   => 'invalidateForModuleChange',
            ThemeActivatedEvent::class              => 'invalidateForThemeChange',
            ThemeSettingChangedEvent::class          => 'invalidateForThemeChange',
            ClearShopCacheEvent::class              => 'invalidateForShopCacheClear',
        ];
    }

    private function invalidate(int $shopId): void
    {
        $this->templateChainCache->invalidate($shopId);
    }
}
