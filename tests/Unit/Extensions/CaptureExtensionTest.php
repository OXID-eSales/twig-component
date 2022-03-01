<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\Twig\Extensions\CaptureExtension;
use OxidEsales\Twig\TokenParser\CaptureTokenParser;
use PHPUnit\Framework\TestCase;

final class CaptureExtensionTest extends TestCase
{
    /**
     * @var CaptureExtension
     */
    private $CaptureExtension;

    protected function setUp(): void
    {
        $this->CaptureExtension = new CaptureExtension();
        parent::setUp();
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\CaptureExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $tokenParser = $this->CaptureExtension->getTokenParsers();
        $this->assertInstanceOf(CaptureTokenParser::class, $tokenParser[0]);
    }
}
