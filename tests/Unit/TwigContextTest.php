<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\Twig\TwigContext;
use PHPUnit\Framework\TestCase;

final class TwigContextTest extends TestCase
{
    private const THEME_ID = 'theme-id';

    public function testGetIsDebugReturnsConfiguredDebugMode(): void
    {
        $twigContext = new TwigContext(true, self::THEME_ID);

        $this->assertTrue($twigContext->getIsDebug());
    }

    public function testGetActiveThemeIdWithNoThemeWillThrow(): void
    {
        $twigContext = new TwigContext(false, '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdReturnsActiveTheme(): void
    {
        $twigContext = new TwigContext(false, self::THEME_ID);

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }
}
