<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Test\NodeTestCase;

abstract class AbstractOxidTwigTestCase extends NodeTestCase
{
    abstract public static function getOxidTwigTests();

    public function getTests()
    {
    }

    #[DataProvider('getOxidTwigTests')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }
}
