<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\Twig\TwigContextInterface;
use OxidEsales\Twig\TwigEngineConfiguration;
use PHPUnit\Framework\TestCase;

final class TwigEngineConfigurationTest extends TestCase
{
    public function testGetParameters(): void
    {
        $shopTemplateCacheService = $this->createConfiguredMock(
            ShopTemplateCacheServiceInterface::class,
            ['getCacheDirectory' => 'dummy_cache_dir']
        );
        $twigContext = $this->createConfiguredMock(
            TwigContextInterface::class,
            ['getIsDebug' => true]
        );
        $engineConfiguration = new TwigEngineConfiguration($twigContext, $shopTemplateCacheService, false);

        $parameters = $engineConfiguration->getParameters();

        $this->assertEquals(
            ['debug' => true, 'cache' => 'dummy_cache_dir'],
            $parameters
        );
    }
}
