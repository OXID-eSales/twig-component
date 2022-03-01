<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\StyleLogic;
use OxidEsales\Twig\Extensions\StyleExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class StyleExtensionTest extends TestCase
{
    /**
     * @covers StyleLogic::collectStyleSheets
     * @dataProvider dataProvider
     *
     * @param $params
     * @param $isDynamic
     */
    public function testCollectStyleSheets($params, $isDynamic): void
    {
        $styleExtension = $this->getStyleExtensionMock($params, $isDynamic);
        $env = $this->getTwigEnvironment($isDynamic);
        $styleExtension->style($env, $params);
    }

    public function dataProvider(): array
    {
        return [
            [['foo' => 'bar', '__oxid_include_dynamic' => true], true],
            [['foo' => 'bar', '__oxid_include_dynamic' => false], false],
            [['foo' => 'bar'], false]
        ];
    }

    private function getTwigEnvironment($isDynamic): Environment
    {
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $env = new Environment($loader, []);
        $env->addGlobal('__oxid_include_dynamic', $isDynamic);
        return $env;
    }

    private function getStyleExtensionMock(array $params, bool $isDynamic): StyleExtension
    {
        /** @var StyleLogic $styleLogic */
        $styleLogic = $this->getMockBuilder(StyleLogic::class)->disableOriginalConstructor()->getMock();
        $styleLogic->method('collectStyleSheets')->willReturn([]);
        $styleLogic->expects($this->once())->method('collectStyleSheets')->with($params, $isDynamic);
        return new StyleExtension($styleLogic);
    }
}
