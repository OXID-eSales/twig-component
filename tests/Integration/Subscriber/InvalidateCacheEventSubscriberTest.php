<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;

final class InvalidateCacheEventSubscriberTest extends TestCase
{
    use ContainerTrait;

    public function testCacheRemovedAfterModuleActivation(): void
    {
        $this->installTestModule();
        $this->putSomethingToTwigCache();

        $this->get(ModuleActivationServiceInterface::class)->activate('testModule', 1);

        $this->assertFalse($this->cacheDirectoryExists());
    }

    public function testCacheRemovedAfterModuleDeactivation(): void
    {
        $this->installTestModule();
        $this->get(ModuleActivationServiceInterface::class)->activate('testModule', 1);

        $this->putSomethingToTwigCache();

        $this->get(ModuleActivationServiceInterface::class)->deactivate('testModule', 1);

        $this->assertFalse($this->cacheDirectoryExists());
    }

    private function putSomethingToTwigCache(): void
    {
        mkdir($this->getCacheDir());
    }

    private function cacheDirectoryExists(): bool
    {
        return is_dir($this->getCacheDir());
    }

    private function getCacheDir(): string
    {
        return $this->get(TwigContextInterface::class)->getCacheDir();
    }

    private function installTestModule(): void
    {
        $this->get(ModuleInstallerInterface::class)->install(
            new OxidEshopPackage(
                __DIR__ . '/Fixtures/testModule'
            )
        );
    }
}
