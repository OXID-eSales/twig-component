<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\Twig\Extensions\HasRightsExtension;
use OxidEsales\Twig\Node\HasRightsNode;
use OxidEsales\Twig\TokenParser\HasRightsTokenParser;
use PHPUnit\Framework\TestCase;

class HasRightsExtensionTest extends TestCase
{
    /**
     * @var HasRightsExtension
     */
    private $hasRightsExtension;

    protected function setUp()
    {
        $this->hasRightsExtension = new HasRightsExtension(new HasRightsTokenParser(HasRightsNode::class));
        parent::setUp();
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\HasRightsExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $tokenParser = $this->hasRightsExtension->getTokenParsers();
        $this->assertInstanceOf(HasRightsTokenParser::class, $tokenParser[0]);
    }
}
