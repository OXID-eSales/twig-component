<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatPriceLogic;
use OxidEsales\Twig\Extensions\FormatPriceExtension;

final class FormatPriceExtensionTest extends AbstractExtensionTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $formatPriceLogic = new FormatPriceLogic();
        $this->extension = new FormatPriceExtension($formatPriceLogic);
    }

    public function priceProvider(): array
    {
        return [
            ['{{ format_price(100) }}', '100,00 €'],
            ['{{ format_price(100, {"currency" : {"sign" : "$"}}) }}', '100,00 $'],
        ];
    }

    /**
     *
     * @dataProvider priceProvider
     * @covers FormatPriceExtension::formatPrice
     */
    public function testFormatPrice($template, $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
