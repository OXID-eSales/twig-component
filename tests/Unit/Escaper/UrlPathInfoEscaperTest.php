<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\UrlPathInfoEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class UrlPathInfoEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new UrlPathInfoEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    public static function escapeProvider(): array
    {
        return [
            [
                "http://hans:geheim@www.example.org:80/demo/example.cgi?land=de&stadt=aa",
                "http%3A//hans%3Ageheim%40www.example.org%3A80/demo/example.cgi%3Fland%3Dde%26stadt%3Daa"
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
        $this->assertEquals('urlpathinfo', $this->escaper->getStrategy());
    }
}
