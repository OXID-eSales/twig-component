<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\Event\AdminModeChangedEvent;
use OxidEsales\Twig\Loader\FilesystemLoader;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminModeChangeEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FilesystemLoader $filesystemLoader,
        private TemplateDirectoryResolverInterface $templateDirectoryResolver,
    ) {
    }

    public function reloadTemplatePaths(): void
    {
        $this->filesystemLoader->setPaths([]);
        foreach ($this->templateDirectoryResolver->getTemplateDirectories() as $namespacedDirectory) {
            $this->filesystemLoader->addPath(
                $namespacedDirectory->getDirectory(),
                $namespacedDirectory->getNamespace()
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AdminModeChangedEvent::class => 'reloadTemplatePaths',
        ];
    }
}
