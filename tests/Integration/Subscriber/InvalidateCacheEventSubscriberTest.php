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
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class InvalidateCacheEventSubscriberTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    public function setUp()
    {
        $this->databaseRestorer = new DatabaseRestorer();
        $this->databaseRestorer->dumpDB(__CLASS__);
    }

    protected function tearDown()
    {
        $this->databaseRestorer->restoreDB(__CLASS__);
    }

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
        $this->get(ModuleInstallerInterface::class)->install(new OxidEshopPackage(
                'testModule',
                __DIR__ . '/Fixtures/testModule')
        );
    }
}
