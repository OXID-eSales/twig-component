<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\WordwrapLogic;
use OxidEsales\Twig\Extensions\Filters\WordwrapExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;
use Twig\Extension\AbstractExtension;

final class WordwrapExtensionTest extends AbstractExtensionTestCase
{
    protected AbstractExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new WordwrapExtension(new WordwrapLogic());
    }

    public static function nonAsciiProvider(): array
    {
        return [
            ['{{ "HÖ HÖ"|wordwrap(2) }}', "HÖ\nHÖ"],
            ['{{ "HÖa HÖa"|wordwrap(2, "\n", true) }}', "HÖ\na\nHÖ\na"],
            ['{{ "HÖaa HÖaa"|wordwrap(3, "\n", true) }}', "HÖa\na\nHÖa\na"],
            ['{{ "HÖa HÖa"|wordwrap(2) }}', "HÖa\nHÖa"]
        ];
    }

    /**
     * @dataProvider nonAsciiProvider
     */
    public function testWordWrapWithNonAscii(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    //phpcs:disable
    public static function asciiProvider(): array
    {
        return [
            ['{{ "aaa aaa"|wordwrap(2) }}', "aaa\naaa"],
            ['{{ "aaa aaa"|wordwrap(2, "\n", true) }}', "aa\na\naa\na"],
            ['{{ "aaa aaa a"|wordwrap(5) }}', "aaa\naaa a"],
            ['{{ "aaa aaa"|wordwrap(5, "\n", true) }}', "aaa\naaa"],
            ['{{ "   aaa    aaa"|wordwrap(2) }}', "  \naaa\n  \naaa"],
            ['{{ "   aaa    aaa"|wordwrap(2, "\n", true) }}', "  \naa\na \n \naa\na"],
            ['{{ "   aaa    aaa"|wordwrap(5) }}', "  \naaa  \n aaa"],
            ['{{ "   aaa    aaa"|wordwrap(5, "\n", true) }}', "  \naaa  \n aaa"],
            [
                "{{ 'Pellentesque nisl non condimentum cursus.\n  consectetur a diam sit.\n finibus diam eu libero lobortis.\neu   ex   sit'|wordwrap(10, \"\\n\", true) }}",
                "Pellentesq\nue nisl\nnon\ncondimentu\nm cursus.\n \nconsectetu\nr a diam\nsit.\n finibus\ndiam eu\nlibero\nlobortis.\neu   ex  \nsit"
            ]
        ];
    }
    //phpcs:enable

    /**
     * @dataProvider asciiProvider
     */
    public function testWordWrapAscii(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
