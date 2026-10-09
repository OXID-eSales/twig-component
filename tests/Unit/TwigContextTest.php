<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\Twig\TwigContext;
use PHPUnit\Framework\TestCase;

final class TwigContextTest extends TestCase
{
    private const THEME_ID = 'theme-id';

    public function testGetActiveThemeIdWithNoFrontendThemeWillThrow(): void
    {
        $twigContext = new TwigContext($this->createConfig(isAdmin: false), '', '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithFrontendThemeReturnsActiveTheme(): void
    {
        $twigContext = new TwigContext($this->createConfig(isAdmin: false), self::THEME_ID, '');

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    public function testGetActiveThemeIdWithEmptyAdminThemeWillThrow(): void
    {
        $twigContext = new TwigContext($this->createConfig(isAdmin: true), self::THEME_ID, '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithAdminThemeReturnsConfiguredAdminTheme(): void
    {
        $twigContext = new TwigContext($this->createConfig(isAdmin: true), 'frontend-theme', self::THEME_ID);

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    private function createConfig(bool $isAdmin): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn($isAdmin);

        return $config;
    }
}
