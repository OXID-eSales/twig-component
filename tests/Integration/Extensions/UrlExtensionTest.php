<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\AddUrlParametersLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\SeoUrlLogic;
use OxidEsales\Twig\Extensions\UrlExtension;

/**
 * Class UrlExtensionTest
 */
class UrlExtensionTest extends AbstractExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setLanguage(0);
        $this->extension = new UrlExtension(new SeoUrlLogic(), new AddUrlParametersLogic());
    }

    /**
     * @param string $template
     * @param string $expected
     * @param array $variables
     *
     * @dataProvider getSeoUrlTests
     */
    public function testSeoUrl(string $template, string $expected, array $variables = []): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @param string $template
     * @param string $expected
     * @param array $variables
     *
     * @dataProvider getAddUrlParametersTests
     */
    public function testAddUrlParameters(string $template, string $expected, array $variables = []): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @return array
     */
    public function getSeoUrlTests(): array
    {
        return [
            [
                "{{ seo_url({ ident: \"server.local?df=ab\", params: \"order=abc\" }) }}",
                "server.local?df=ab&amp;order=abc"
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAddUrlParametersTests(): array
    {
        return [
            [
                "{{ 'abc'|add_url_parameters('de=fg&hi=&jk=lm') }}",
                "abc?de=fg&amp;hi=&amp;jk=lm"
            ],
        ];
    }
}
