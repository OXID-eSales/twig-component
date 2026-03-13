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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InvalidateTemplateChainCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TemplateChainCacheInterface $templateChainCache)
    {
    }

    public function invalidateTemplateChainCache(
        ModuleSetupEvent|ModuleConfigurationChangedEvent|ThemeSettingChangedEvent|ThemeActivatedEvent $event
    ): void
    {
        $this->templateChainCache->invalidate($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::class   => 'invalidateTemplateChainCache',
            FinalizingModuleDeactivationEvent::class => 'invalidateTemplateChainCache',
            ModuleConfigurationChangedEvent::class   => 'invalidateTemplateChainCache',
            ThemeSettingChangedEvent::class          => 'invalidateTemplateChainCache',
            ThemeActivatedEvent::class              => 'invalidateTemplateChainCache',
        ];
    }
}
