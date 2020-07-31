<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extension;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeWidgetLogic;
use OxidEsales\Twig\Extensions\IncludeWidgetExtension;
use PHPUnit\Framework\TestCase;

final class IncludeWidgetExtensionTest extends TestCase
{
    /**
     * @var IncludeWidgetExtension
     */
    protected $includeWidgetExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $includeWidgetLogic = new IncludeWidgetLogic();
        $this->includeWidgetExtension = new IncludeWidgetExtension($includeWidgetLogic);
    }

    /**
     * @covers       \OxidEsales\Twig\Extensions\IncludeWidgetExtension::includeWidget
     */
    public function testIncludeWidget()
    {
        $widgetControl = $this->createMock(\OxidEsales\Eshop\Core\WidgetControl::class);
        $widgetControl->expects($this->any())->method("start")->will($this->returnValue('html'));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\WidgetControl::class, $widgetControl);

        $actual = $this->includeWidgetExtension->includeWidget(['cl' => 'oxwTagCloud', 'blShowTags' => 1]);
        $this->assertEquals('html', $actual);
    }

}
