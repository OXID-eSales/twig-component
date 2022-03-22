<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Escaper;

use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Escaper\MailEscaper;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class MailEscaperTest extends TestCase
{
    /** @var EscaperInterface */
    private $escaper;

    /** @var Environment */
    private $environment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->escaper = new MailEscaper();
        $this->environment = $this->createMock(Environment::class);
    }

    /**
     * @return array
     */
    public function escapeProvider(): array
    {
        return [
            [
                'simple@example.com',
                'simple [AT] example [DOT] com'
            ],
            [
                'very.common@example.com',
                'very [DOT] common [AT] example [DOT] com'
            ],
            [
                'disposable.style.email.with+symbol@example.com',
                'disposable [DOT] style [DOT] email [DOT] with+symbol [AT] example [DOT] com'
            ],
            [
                'other.email-with-hyphen@example.com',
                'other [DOT] email-with-hyphen [AT] example [DOT] com'
            ],
            [
                'fully-qualified-domain@example.com',
                'fully-qualified-domain [AT] example [DOT] com'
            ],
            [
                'user.name+tag+sorting@example.com',
                'user [DOT] name+tag+sorting [AT] example [DOT] com'
            ],
            [
                'x@example.com',
                'x [AT] example [DOT] com'
            ],
            [
                'example-indeed@strange-example.com',
                'example-indeed [AT] strange-example [DOT] com'
            ],
            [
                'admin@mailserver1',
                'admin [AT] mailserver1'
            ],
            [
                'example@s.example',
                'example [AT] s [DOT] example'
            ],
            [
                '" "@example.org',
                '" " [AT] example [DOT] org'
            ],
            [
                '"john..doe"@example.org',
                '"john [DOT]  [DOT] doe" [AT] example [DOT] org'
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
        $this->assertEquals('mail', $this->escaper->getStrategy());
    }
}
