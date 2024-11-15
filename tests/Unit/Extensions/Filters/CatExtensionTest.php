<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Filters;

use OxidEsales\Twig\Extensions\Filters\CatExtension;
use PHPUnit\Framework\TestCase;

final class CatExtensionTest extends TestCase
{
    public function testCat(): void
    {
        $string = 'foo';
        $cat = 'bar';

        $actual = (new CatExtension())->cat($string, $cat);

        $this->assertEquals($string . $cat, $actual);
    }
}
