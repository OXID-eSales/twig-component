<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\DecEntityEscaper;
use OxidEsales\Twig\Escaper\EscaperInterface;
use Twig\Environment;

/**
 * Class DecEntityEscaperTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class DecEntityEscaperTest extends \PHPUnit\Framework\TestCase
{
    /** @var EscaperInterface */
    private $escaper;

    /** @var Environment */
    private $environment;

    public function setUp()
    {
        parent::setUp();
        $this->escaper = new DecEntityEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            [
                "A 'quote' is <b>bold</b>",
                "&#65;&#32;&#39;&#113;&#117;&#111;&#116;&#101;&#39;&#32;&#105;&#115;&#32;&#60;&#98;&#62;&#98;&#111;&#108;&#100;&#60;&#47;&#98;&#62;"
            ]
        ];
    }

    /**
     * @param string $string
     * @param string $expected
     *
     * @dataProvider escapeProvider
     */
    public function testEscape($string, $expected)
    {
        $this->assertEquals($expected, $this->escaper->escape($this->environment, $string, 'UTF-8'));
    }

    public function testGetStrategy()
    {
        $this->assertEquals('decentity', $this->escaper->getStrategy());
    }
}
