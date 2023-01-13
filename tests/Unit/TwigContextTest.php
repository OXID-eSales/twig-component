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
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class TwigContextTest extends TestCase
{
    use ProphecyTrait;

    private TwigContextInterface $twigContext;
    private Config|ObjectProphecy $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = $this->prophesize(Config::class);
        $this->twigContext = new TwigContext(
            $this->config->reveal(),
            ''
        );
    }

    public function testGetActiveThemeIdWithNoFrontendThemeWillThrow(): void
    {
        $this->config->isAdmin()->willReturn(false);
        $this->config->getConfigParam('sCustomTheme')->willReturn(null);
        $this->config->getConfigParam('sTheme')->willReturn(null);

        $this->expectException(InvalidThemeNameException::class);

        $this->twigContext->getActiveThemeId();
    }

    public function testGetActiveThemeIdWithParentFrontendTheme(): void
    {
        $parentThemeId = 123;
        $this->config->isAdmin()->willReturn(false);
        $this->config->getConfigParam('sCustomTheme')->willReturn(null);
        $this->config->getConfigParam('sTheme')->willReturn($parentThemeId);

        $themeId = $this->twigContext->getActiveThemeId();

        $this->assertEquals($parentThemeId, $themeId);
    }

    public function testGetActiveThemeIdWithChildFrontendTheme(): void
    {
        $childThemeId = 123;
        $this->config->isAdmin()->willReturn(false);
        $this->config->getConfigParam('sCustomTheme')->willReturn($childThemeId);
        $this->config->getConfigParam('sTheme')->willReturn(null);

        $themeId = $this->twigContext->getActiveThemeId();

        $this->assertEquals($childThemeId, $themeId);
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

        $themeId = (new TwigContext($this->config->reveal(), $adminThemeId))->getActiveThemeId();

        $this->assertEquals($adminThemeId, $themeId);
    }
}
