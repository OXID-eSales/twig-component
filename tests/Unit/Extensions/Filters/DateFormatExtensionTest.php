<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\DateFormatHelper;
use OxidEsales\Twig\Extensions\Filters\DateFormatExtension;
use PHPUnit\Framework\TestCase;

final class DateFormatExtensionTest extends TestCase
{
    private DateFormatExtension $dateFormatExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $dateFormatHelper = new DateFormatHelper();
        $this->dateFormatExtension = new DateFormatExtension($dateFormatHelper);
    }

    public static function provider(): array
    {
        return [

            //dummy data
            ['', '', '', null],
            ['foo', '', '', null],
            ['', 'foo', '', null],
            ['', '', 'foo', false],
            ['foo', 'foo', '', 'foo'],
            ['foo', 'foo', 'foo', 'foo'],

            //provided input string
            [20_181_201_101_525, '%Y-%m-%d %H:%M:%S', '', '2018-12-01 10:15:25'],           //mysql format
            [1_543_850_519, '%Y-%m-%d %H:%M:%S', '', '2018-12-03 16:21:59'],               //time()
            ['Dec 03 15:21:59 2018', '%Y-%m-%d %H:%M:%S', '', '2018-12-03 15:21:59'],   //string time

            //no input string provided, default date used
            ['', '%Y-%m-%d %H:%M:%S', 20_181_201_101_525, '2018-12-01 10:15:25'],           //mysql format
            ['', '%Y-%m-%d %H:%M:%S', 1_543_850_519, '2018-12-03 16:21:59'],               //time()
            ['', '%Y-%m-%d %H:%M:%S', 'Dec 03 15:21:59 2018', '2018-12-03 15:21:59'],   //string time
        ];
    }

    /**
     * @param mixed  $string
     * @param string $format
     * @param string $default_date
     * @param string $expectedDate
     *
     * @dataProvider provider
     * @covers \OxidEsales\Twig\Extensions\Filters\DateFormatExtension::dateFormat
     */
    public function testDateFormat($string, $format, $default_date, $expectedDate): void
    {
        $actualDate = $this->dateFormatExtension->dateFormat($string, $format, $default_date);
        $this->assertEquals($expectedDate, $actualDate);
    }
}
