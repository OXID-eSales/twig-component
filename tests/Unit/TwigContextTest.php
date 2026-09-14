<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\TwigContext;
use PHPUnit\Framework\TestCase;

final class TwigContextTest extends TestCase
{
    private const THEME_ID = 'theme-id';

    public function testGetIsDebugReturnsConfiguredDebugMode(): void
    {
        $twigContext = new TwigContext($this->createContext(isAdmin: false), true, self::THEME_ID, '');

        $this->assertTrue($twigContext->getIsDebug());
    }

    public function testGetActiveThemeIdWithNoFrontendThemeWillThrow(): void
    {
        $twigContext = new TwigContext($this->createContext(isAdmin: false), false, '', '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithFrontendThemeReturnsActiveTheme(): void
    {
        $twigContext = new TwigContext($this->createContext(isAdmin: false), false, self::THEME_ID, '');

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    public function testGetActiveThemeIdWithEmptyAdminThemeWillThrow(): void
    {
        $twigContext = new TwigContext($this->createContext(isAdmin: true), false, self::THEME_ID, '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithAdminThemeReturnsConfiguredAdminTheme(): void
    {
        $twigContext = new TwigContext($this->createContext(isAdmin: true), false, 'frontend-theme', self::THEME_ID);

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    private function createContext(bool $isAdmin): ContextInterface
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('isAdmin')->willReturn($isAdmin);

        return $context;
    }
}
