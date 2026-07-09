<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidThemeNameException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\Twig\TwigContext;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class TwigContextTest extends TestCase
{
    use ProphecyTrait;

    private TwigContextInterface $twigContext;
    private Config|ObjectProphecy $config;
    private ThemeStateServiceInterface|ObjectProphecy $themeStateService;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = $this->prophesize(Config::class);
        $this->themeStateService = $this->prophesize(ThemeStateServiceInterface::class);
        $this->twigContext = new TwigContext(
            $this->config->reveal(),
            $this->themeStateService->reveal(),
            ''
        );
    }

    public function testGetActiveThemeIdWithNoFrontendThemeWillThrow(): void
    {
        $shopId = 1;
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveThemeId($shopId)->willThrow(ActiveThemeNotFoundException::class);

        $this->expectException(InvalidThemeNameException::class);

        $this->twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithFrontendTheme(): void
    {
        $shopId = 1;
        $themeId = 'theme-id';
        $this->config->isAdmin()->willReturn(false);
        $this->config->getShopId()->willReturn($shopId);
        $this->themeStateService->getActiveThemeId($shopId)->willReturn($themeId);

        $result = $this->twigContext->getActiveThemeId();

        $this->assertEquals($themeId, $result);
    }

    public function testGetActiveThemeIdWithEmptyAdminThemeWillThrow(): void
    {
        $this->config->isAdmin()->willReturn(true);

        $this->expectException(InvalidThemeNameException::class);

        $this->twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithNonEmptyAdminTheme(): void
    {
        $adminThemeId = 'theme-id';
        $this->config->isAdmin()->willReturn(true);

        $themeId = (new TwigContext(
            $this->config->reveal(),
            $this->themeStateService->reveal(),
            $adminThemeId
        ))->getActiveThemeId();

        $this->assertEquals($adminThemeId, $themeId);
    }
}