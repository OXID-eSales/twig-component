<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatCurrencyLogic;
use OxidEsales\Twig\Extensions\Filters\FormatCurrencyExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;

/**
 * Class FormatCurrencyExtensionTest
 */
class FormatCurrencyExtensionTest extends AbstractExtensionTest
{
    /** @var FormatCurrencyExtension */
    protected $extension;

    public function setUp()
    {
        $this->extension = new FormatCurrencyExtension(new FormatCurrencyLogic());
    }

    public function testNumberFormat()
    {
        $template = "{{ 'EUR@ 1.00@ .@ ,@ EUR@ 2'|format_currency(25000000.5584) }}";
        $expected = '25,000,000.56';

        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
