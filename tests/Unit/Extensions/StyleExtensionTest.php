<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\StyleLogic;
use OxidEsales\Twig\Extensions\StyleExtension;
use \PHPUnit\Framework\TestCase;

class StyleExtensionTest extends TestCase
{
    /**
     * @covers       \OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\StyleLogic::collectStyleSheets
     * @dataProvider dataProvider
     *
     * @param $params
     * @param $isDynamic
     */
    public function testCollectStyleSheets($params, $isDynamic)
    {
        $styleExtension = $this->getStyleExtensionMock($params, $isDynamic);
        $env = $this->getTwigEnvironment($isDynamic);
        $styleExtension->style($env, $params);
    }

    public function dataProvider()
    {
        return [
            [['foo' => 'bar', '__oxid_include_dynamic' => true], true],
            [['foo' => 'bar', '__oxid_include_dynamic' => false], false],
            [['foo' => 'bar'], false]
        ];
    }

    private function getTwigEnvironment($isDynamic)
    {
        /** @var \Twig_LoaderInterface $loader */
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $env = new \Twig_Environment($loader, []);
        $env->addGlobal('__oxid_include_dynamic', $isDynamic);
        return $env;
    }

    /**
     * @param array $params
     * @param bool  $isDynamic
     *
     * @return StyleExtension
     */
    private function getStyleExtensionMock($params, $isDynamic)
    {
        /** @var StyleLogic $styleLogic */
        $styleLogic = $this->getMockBuilder('OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\StyleLogic')->disableOriginalConstructor()->getMock();
        $styleLogic->method('collectStyleSheets')->willReturn([]);
        $styleLogic->expects($this->once())->method('collectStyleSheets')->with($params, $isDynamic);
        $styleExtension = new StyleExtension($styleLogic);

        return $styleExtension;
    }
}
