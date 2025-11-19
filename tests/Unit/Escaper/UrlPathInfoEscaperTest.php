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

final class UrlPathInfoEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new UrlPathInfoEscaper();
    }

    public function testEscape(): void
    {
        $string = 'http://hans:geheim@www.example.org:80/demo/example.cgi?land=de&stadt=aa';
        $expected = 'http%3A//hans%3Ageheim%40www.example.org%3A80/demo/example.cgi%3Fland%3Dde%26stadt%3Daa';
        $this->assertEquals($expected, $this->escaper->escape($string, 'UTF-8'));
    }

    public function testGetStrategy(): void
    {
        $this->assertEquals('urlpathinfo', $this->escaper->getStrategy());
    }
}
