<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\HexEntityEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class HexEntityEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new HexEntityEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    public static function escapeProvider(): array
    {
        return [
            [
                "A 'quote' is <b>bold</b>",
                "&#x41;&#x20;&#x27;&#x71;&#x75;&#x6f;&#x74;&#x65;&#x27;&#x20;&#x69;&#x73;&#x20;&#x3c;&#x62;&#x3e;&#x62;&#x6f;&#x6c;&#x64;&#x3c;&#x2f;&#x62;&#x3e;"
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
        $this->assertEquals('hexentity', $this->escaper->getStrategy());
    }
}
