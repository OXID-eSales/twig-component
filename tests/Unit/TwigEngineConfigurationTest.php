<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\TwigContextInterface;
use OxidEsales\Twig\TwigEngineConfiguration;
use PHPUnit\Framework\TestCase;

final class TwigEngineConfigurationTest extends TestCase
{
    public function testGetParameters(): void
    {
        $context = $this->createConfiguredMock(
            ContextInterface::class,
            ['getTemplateCacheDirectory' => 'dummy_cache_dir']
        );
        $twigContext = $this->createConfiguredMock(
            TwigContextInterface::class,
            ['getIsDebug' => true]
        );
        $engineConfiguration = new TwigEngineConfiguration($context, $twigContext);

        $parameters = $engineConfiguration->getParameters();

        $this->assertEquals(
            ['debug' => true, 'cache' => 'dummy_cache_dir'],
            $parameters
        );
    }
}
