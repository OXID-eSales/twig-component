<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\SmartWordwrapLogic;
use OxidEsales\Twig\Extensions\Filters\SmartWordwrapExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SmartWordwrapExtensionTest extends TestCase
{
    public static function provider(): array
    {
        return [
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas risus ipsum, ornare id scelerisque non, porta nec nulla.'
                ],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas risus ipsum,
ornare id scelerisque non, porta nec nulla.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 20
                ],
                'Lorem ipsum dolor
sit amet,
consectetur
adipiscing elit.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 20,
                    'break' => '<br/>'
                ],
                'Lorem ipsum dolor<br/>sit amet,<br/>consectetur<br/>adipiscing elit.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 10,
                    'cutRows' => 7
                ],
                'Lorem
ipsum
dolor sit
amet,
consectetu
r
adipisc...'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 20,
                    'tolerance' => -30
                ],
                'Lorem ipsum dolor
sit amet,
consectetur
adipiscing elit.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 20,
                    'tolerance' => 30
                ],
                'Lorem ipsum dolor
sit amet,
consectetur
adipiscing elit.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 20,
                    'tolerance' => 150
                ],
                'Lorem ipsum dolor
sit amet,
consectetur
adipiscing elit.'
            ],
            [
                [
                    'string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'length' => 10,
                    'cutRows' => 7,
                    'etc' => '[...]'
                ],
                'Lorem
ipsum
dolor sit
amet,
consectetu
r
adipi[...]'
            ]
        ];
    }

    #[DataProvider('provider')]
    public function testSmartWordWrap(array $params, string $expectedString): void
    {
        $smartWordWrapLogic = new SmartWordwrapLogic();
        $smartWordWrapExtension = new SmartWordwrapExtension($smartWordWrapLogic);
        $string = $params['string'];
        $length = $params['length'] ?? 80;
        $break = $params['break'] ?? "\n";
        $cutRows = $params['cutRows'] ?? 0;
        $tolerance = $params['tolerance'] ?? 0;
        $etc = $params['etc'] ?? '...';

        $actualString = $smartWordWrapExtension->smartWordwrap($string, $length, $break, $cutRows, $tolerance, $etc);
        $this->assertEquals($expectedString, $actualString);
    }
}
