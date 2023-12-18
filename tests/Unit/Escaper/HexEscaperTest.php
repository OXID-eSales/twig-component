<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\HexEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class HexEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new HexEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    public static function escapeProvider(): array
    {
        return [
            ["A 'quote' is <b>bold</b>", "%41%20%27%71%75%6f%74%65%27%20%69%73%20%3c%62%3e%62%6f%6c%64%3c%2f%62%3e"]
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
        $this->assertEquals('hex', $this->escaper->getStrategy());
    }
}
