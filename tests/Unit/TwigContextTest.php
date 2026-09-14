<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\Twig\TwigContext;
use PHPUnit\Framework\TestCase;

final class TwigContextTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'theme-id';

    public function testGetActiveThemeIdWithNoFrontendThemeWillThrow(): void
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveThemeId')->willThrowException(new ActiveThemeNotFoundException());
        $twigContext = new TwigContext($this->createConfig(isAdmin: false), $activeThemeProvider, '');

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithFrontendThemeReturnsActiveThemeOfCurrentShop(): void
    {
        $activeThemeProvider = $this->createMock(ActiveThemeProviderInterface::class);
        $activeThemeProvider
            ->expects($this->once())
            ->method('getActiveThemeId')
            ->with(self::SHOP_ID)
            ->willReturn(self::THEME_ID);
        $twigContext = new TwigContext($this->createConfig(isAdmin: false), $activeThemeProvider, '');

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    public function testGetActiveThemeIdWithEmptyAdminThemeWillThrow(): void
    {
        $twigContext = new TwigContext(
            $this->createConfig(isAdmin: true),
            $this->createStub(ActiveThemeProviderInterface::class),
            ''
        );

        $this->expectException(InvalidThemeNameException::class);

        $twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithAdminThemeReturnsConfiguredAdminTheme(): void
    {
        $activeThemeProvider = $this->createMock(ActiveThemeProviderInterface::class);
        $activeThemeProvider->expects($this->never())->method('getActiveThemeId');
        $twigContext = new TwigContext($this->createConfig(isAdmin: true), $activeThemeProvider, self::THEME_ID);

        $this->assertSame(self::THEME_ID, $twigContext->getActiveThemeId());
    }

    private function createConfig(bool $isAdmin): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isAdmin')->willReturn($isAdmin);
        $config->method('getShopId')->willReturn(self::SHOP_ID);

        return $config;
    }
}
