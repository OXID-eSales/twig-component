<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\HtmlAllEscaper;
use Twig\Environment;

/**
 * Class HtmlAllEscaperTest
 */
class HtmlAllEscaperTest extends \PHPUnit\Framework\TestCase
{

    /** @var EscaperInterface */
    private $escaper;

    /** @var Environment */
    private $environment;

    public function setUp()
    {
        parent::setUp();
        $this->escaper = new HtmlAllEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            ["A 'quote' is <b>bold</b>", "A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;"]
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
        $this->assertEquals('htmlall', $this->escaper->getStrategy());
    }
}
