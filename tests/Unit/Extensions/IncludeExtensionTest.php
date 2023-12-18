<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeDynamicLogic;
use OxidEsales\Twig\Extensions\IncludeExtension;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class IncludeExtensionTest extends TestCase
{
    use ProphecyTrait;

    private IncludeExtension $includeExtension;

    public function setUp(): void
    {
        $this->includeExtension = new IncludeExtension(
            new IncludeDynamicLogic(),
            $this->prophesize(TemplateChainResolverInterface::class)->reveal(),
        );
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\IncludeExtension::includeDynamicPrefix
     * @dataProvider dataProviderTestIncludeDynamicPrefix
     */
    public function testIncludeDynamicPrefix(array $parameters, array $expected): void
    {
        $this->assertEquals($this->includeExtension->includeDynamicPrefix($parameters), $expected);
    }

    public static function dataProviderTestIncludeDynamicPrefix(): array
    {
        return [
            [[], []],
            [['param1' => 'val1', 'param2' => 2], ['_param1' => 'val1', '_param2' => 2]],
            [['type' => 'custom'], []],
            [
                ['type' => 'custom', 'param1' => 'val1', 'param2' => 2],
                ['_custom_param1' => 'val1', '_custom_param2' => 2],
            ],
            [['type' => 'custom', 'file' => 'file.tpl'], []],
            [['type' => 'custom', 'file' => 'file.tpl', 'param' => 'val'], ['_custom_param' => 'val']]
        ];
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\IncludeExtension::renderForCache
     * @dataProvider dataProviderTestRenderForCache
     */
    public function testRenderForCache(array $parameters, string $expected): void
    {
        $this->assertEquals($this->includeExtension->renderForCache($parameters), $expected);
    }

    public static function dataProviderTestRenderForCache(): array
    {
        return [
            [[], '<oxid_dynamic></oxid_dynamic>'],
            [['param1' => 'val1', 'param2' => 2], '<oxid_dynamic> param1=\'dmFsMQ==\' param2=\'Mg==\'</oxid_dynamic>'],
        ];
    }
}
