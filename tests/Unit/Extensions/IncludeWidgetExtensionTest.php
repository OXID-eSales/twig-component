<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extension;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeWidgetLogic;
use OxidEsales\Twig\Extensions\IncludeWidgetExtension;
use PHPUnit\Framework\TestCase;

final class IncludeWidgetExtensionTest extends TestCase
{
    private IncludeWidgetExtension $includeWidgetExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $includeWidgetLogic = new IncludeWidgetLogic();
        $this->includeWidgetExtension = new IncludeWidgetExtension($includeWidgetLogic);
    }

    public function testIncludeWidget(): void
    {
        $widgetControl = $this->createStub(WidgetControl::class);
        $widgetControl->method('start')->willReturn('html');
        Registry::set(WidgetControl::class, $widgetControl);

        $actual = $this->includeWidgetExtension->includeWidget(['cl' => 'oxwTagCloud', 'blShowTags' => 1]);
        $this->assertEquals('html', $actual);
    }
}
