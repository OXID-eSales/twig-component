<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\DecEntityEscaper;
use OxidEsales\Twig\Escaper\EscaperInterface;
use PHPUnit\Framework\TestCase;

final class DecEntityEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new DecEntityEscaper();
    }

    public function testEscape(): void
    {
        $string = 'A \'quote\' is <b>bold</b>';
        $expected = '&#65;&#32;&#39;&#113;&#117;&#111;&#116;&#101;&#39;&#32;&#105;&#115;&#32;&#60;&#98;&#62;&#98;'
        . '&#111;&#108;&#100;&#60;&#47;&#98;&#62;';
        $this->assertEquals($expected, $this->escaper->escape($string, 'UTF-8'));
    }

    public function testGetStrategy(): void
    {
        $this->assertEquals('decentity', $this->escaper->getStrategy());
    }
}
