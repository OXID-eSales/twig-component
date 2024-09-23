<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\AddUrlParametersLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\SeoUrlLogic;
use OxidEsales\Twig\Extensions\UrlExtension;

final class UrlExtensionTest extends AbstractExtensionTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setLanguage(0);
        $this->extension = new UrlExtension(new SeoUrlLogic(), new AddUrlParametersLogic());
    }

    /**
     * @dataProvider getSeoUrlTests
     */
    public function testSeoUrl(string $template, string $expected, array $variables = []): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @dataProvider getAddUrlParametersTests
     */
    public function testAddUrlParameters(string $template, string $expected, array $variables = []): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    public static function getSeoUrlTests(): array
    {
        return [
            [
                '{{ seo_url({ ident: "server.local?df=ab", params: "order=abc" }) }}',
                'server.local?df=ab&amp;order=abc'
            ],
        ];
    }

    public static function getAddUrlParametersTests(): array
    {
        return [
            [
                "{{ 'abc'|add_url_parameters('de=fg&hi=&jk=lm') }}",
                'abc?de=fg&amp;hi=&amp;jk=lm'
            ],
        ];
    }
}
