<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\WordwrapLogic;
use OxidEsales\Twig\Extensions\Filters\WordwrapExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;

/**
 * Class WordwrapExtensionTest
 */
class WordwrapExtensionTest extends AbstractExtensionTest
{
    /** @var WordwrapExtension */
    protected $extension;

    public function setUp()
    {
        $this->extension = new WordwrapExtension(new WordwrapLogic());
    }

    /**
     * Provides data for testWordWrapWithNonAscii
     *
     * @return array
     */
    public function nonAsciiProvider(): array
    {
        return [
            ['{{ "HÖ HÖ"|wordwrap(2) }}', "HÖ\nHÖ"],
            ['{{ "HÖa HÖa"|wordwrap(2, "\n", true) }}', "HÖ\na\nHÖ\na"],
            ['{{ "HÖaa HÖaa"|wordwrap(3, "\n", true) }}', "HÖa\na\nHÖa\na"],
            ['{{ "HÖa HÖa"|wordwrap(2) }}', "HÖa\nHÖa"]
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider nonAsciiProvider
     */
    public function testWordWrapWithNonAscii($template, $expected)
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * Provides data for testWordWrapAscii
     *
     * @return array
     */
    public function asciiProvider(): array
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

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider asciiProvider
     */
    public function testWordWrapAscii($template, $expected)
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
