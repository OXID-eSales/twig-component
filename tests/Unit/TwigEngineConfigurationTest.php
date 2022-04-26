<?php
/**
 * Created by PhpStorm.
 * User: jskoczek
 * Date: 22/08/18
 * Time: 15:25
 */

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Twig\TwigEngineConfiguration;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TwigEngineConfigurationTest extends TestCase
{

    public function testGetParameters(): void
    {
        $engineConfiguration = $this->getEngineConfiguration();
        $this->assertEquals(['debug' => true, 'cache' => 'dummy_cache_dir'], $engineConfiguration->getParameters());
        $this->assertNotEquals(['debug' => 'foo', 'cache' => 'foo'], $engineConfiguration->getParameters());
    }

    private function getEngineConfiguration(): TwigEngineConfiguration
    {
        /** @var TwigContextInterface|MockObject $context */
        $context = $this->getMockBuilder(TwigContextInterface::class)->getMock();
        $context->method('getIsDebug')->willReturn(true);
        $context->method('getCacheDir')->willReturn('dummy_cache_dir');
        return new TwigEngineConfiguration($context);
    }
}
