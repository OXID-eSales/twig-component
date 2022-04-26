<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Filters;

use OxidEsales\Twig\Extensions\Filters\EncloseExtension;
use PHPUnit\Framework\TestCase;

final class EncloseFilterTest extends TestCase
{
    /**
     * @covers \OxidEsales\Twig\Extensions\Filters\EncloseExtension::enclose
     */
    public function testEnclose(): void
    {
        $string = "foo";
        $encloser = "*";
        $encloseFilter = new EncloseExtension();
        $enclosedString = $encloseFilter->enclose($string, $encloser);
        $this->assertEquals('*foo*', $enclosedString);
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\Filters\EncloseExtension::enclose
     */
    public function testEncloseNoEncloder(): void
    {
        $string = "foo";
        $encloseFilter = new EncloseExtension();
        $enclosedString = $encloseFilter->enclose($string);
        $this->assertEquals('foo', $enclosedString);
    }
}
