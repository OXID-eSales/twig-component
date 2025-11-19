<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\HtmlAllEscaper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HtmlAllEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new HtmlAllEscaper();
    }

    public static function escapeProvider(): array
    {
        return [
            ["A 'quote' is <b>bold</b>", "A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;"]
        ];
    }

    #[DataProvider('escapeProvider')]
    public function testEscape(string $string, string $expected)
    {
        $this->assertEquals($expected, $this->escaper->escape($string, 'UTF-8'));
    }

    public function testGetStrategy()
    {
        $this->assertEquals('htmlall', $this->escaper->getStrategy());
    }
}
