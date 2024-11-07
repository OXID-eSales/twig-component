<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\QuotesEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class QuotesEscaperTest extends TestCase
{
    private EscaperInterface $escaper;

    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new QuotesEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    public function testEscape(): void
    {
        $string = 'A \'quote\' is <b>bold</b>';
        $expected = 'A \\\'quote\\\' is <b>bold</b>';
        $this->assertEquals($expected, $this->escaper->escape($this->environment, $string, 'UTF-8'));
    }

    public function testGetStrategy(): void
    {
        $this->assertEquals('quotes', $this->escaper->getStrategy());
    }
}
