<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Filters;

use OxidEsales\Twig\Extensions\Filters\CatExtension;
use PHPUnit\Framework\TestCase;

class CatExtensionTest extends TestCase
{
    /**
     * @covers \OxidEsales\Twig\Extensions\Filters\CatExtension::cat
     */
    public function testCat(): void
    {
        $catFilter = new CatExtension();
        $string = 'foo';
        $cat = 'bar';
        $actual = $catFilter->cat($string, $cat);
        $this->assertEquals($string . $cat, $actual);
    }
}
