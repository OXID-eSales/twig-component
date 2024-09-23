<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatCurrencyLogic;
use OxidEsales\Twig\Extensions\Filters\FormatCurrencyExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;
use Twig\Extension\AbstractExtension;

final class FormatCurrencyExtensionTest extends AbstractExtensionTestCase
{
    protected AbstractExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new FormatCurrencyExtension(new FormatCurrencyLogic());
    }

    public function testNumberFormat(): void
    {
        $template = "{{ 'EUR@ 1.00@ .@ ,@ EUR@ 2'|format_currency(25000000.5584) }}";
        $expected = '25,000,000.56';

        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
