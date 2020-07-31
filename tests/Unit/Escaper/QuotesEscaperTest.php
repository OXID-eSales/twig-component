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
    /** @var EscaperInterface */
    private $escaper;

    /** @var Environment */
    private $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new QuotesEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            ["A 'quote' is <b>bold</b>", "A \'quote\' is <b>bold</b>"]
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
        $this->assertEquals('quotes', $this->escaper->getStrategy());
    }
}
