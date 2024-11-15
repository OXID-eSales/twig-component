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
    public function testEnclose(): void
    {
        $enclosedString = (new EncloseExtension())->enclose('foo', '*');

        $this->assertEquals('*foo*', $enclosedString);
    }

    public function testEncloseNoEncloser(): void
    {
        $enclosedString = (new EncloseExtension())->enclose('foo');

        $this->assertEquals('foo', $enclosedString);
    }
}
