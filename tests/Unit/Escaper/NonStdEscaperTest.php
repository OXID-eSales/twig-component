<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\NonStdEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class NonStdEscaperTest extends TestCase
{
    /** @var EscaperInterface */
    private $escaper;

    /** @var Environment */
    private $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new NonStdEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            [
                "Zażółć 'gęślą' <b>jaźń</b>",
                "Za&#197;&#188;&#195;&#179;&#197;&#130;&#196;&#135; 'g&#196;&#153;&#197;&#155;l&#196;&#133;' <b>ja&#197;&#186;&#197;&#132;</b>"
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
        $this->assertEquals('nonstd', $this->escaper->getStrategy());
    }
}
