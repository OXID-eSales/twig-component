<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\Twig\Extensions\HasRightsExtension;
use OxidEsales\Twig\Node\HasRightsNode;
use OxidEsales\Twig\TokenParser\HasRightsTokenParser;
use PHPUnit\Framework\TestCase;

final class HasRightsExtensionTest extends TestCase
{
    private HasRightsExtension $hasRightsExtension;

    protected function setUp(): void
    {
        $this->hasRightsExtension = new HasRightsExtension(new HasRightsTokenParser(HasRightsNode::class));
        parent::setUp();
    }

    public function testGetTokenParsers(): void
    {
        $tokenParser = $this->hasRightsExtension->getTokenParsers();
        $this->assertInstanceOf(HasRightsTokenParser::class, $tokenParser[0]);
    }
}
