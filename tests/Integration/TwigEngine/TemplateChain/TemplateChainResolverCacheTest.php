<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class TemplateChainResolverCacheTest extends TestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const MODULE_ID = 'module1';
    private const THEME = 'testTheme';
    private const TEMPLATE_NO_EXTENDS = 'template-no-extends.html.twig';
    private const TEMPLATE_WITH_EXTENDS = 'template-with-extends.html.twig';
    private const MODULE_TEMPLATE_WITH_EXTENDS = '@' . self::MODULE_ID . '/template-with-extends.html.twig';

    private TemplateChainResolverInterface $resolver;
    private TemplateChainCacheInterface $cache;
    private int $shopId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initFixtures(__DIR__);
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);
        $this->resolver = $this->get(TemplateChainResolverInterface::class);
        $this->cache = $this->get(TemplateChainCacheInterface::class);
        $this->shopId = $this->get(BasicContextInterface::class)->getDefaultShopId();
        $this->cache->invalidate($this->shopId);
    }

    protected function tearDown(): void
    {
        $this->uninstallModuleFixture(self::MODULE_ID);
        $this->cache->invalidate($this->shopId);

        parent::tearDown();
    }

    public function testGetLastChildReturnsValueFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::TEMPLATE_WITH_EXTENDS,
            $this->createTemplateChainWithParent(self::TEMPLATE_WITH_EXTENDS, self::MODULE_ID)
        );

        $this->assertSame(
            self::MODULE_TEMPLATE_WITH_EXTENDS,
            $this->resolver->getLastChild(self::TEMPLATE_WITH_EXTENDS)
        );
    }

    public function testGetParentReturnsValueFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::MODULE_TEMPLATE_WITH_EXTENDS,
            $this->createTemplateChainWithParent(self::TEMPLATE_WITH_EXTENDS, self::MODULE_ID)
        );

        $this->assertSame(
            '@__main__/template-with-extends.html.twig',
            $this->resolver->getParent(self::MODULE_TEMPLATE_WITH_EXTENDS)
        );
    }

    public function testHasParentReturnsFalseFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::TEMPLATE_NO_EXTENDS,
            $this->createTemplateChainWithoutParent(self::TEMPLATE_NO_EXTENDS)
        );

        $this->assertFalse($this->resolver->hasParent(self::TEMPLATE_NO_EXTENDS));
    }

    public function testGetLastChildBuildsAndCachesTemplateChainWhenCacheDoesNotExist(): void
    {
        $this->installModuleAndReinitializeFixtures(self::MODULE_ID);

        $lastChild = $this->resolver->getLastChild(self::TEMPLATE_WITH_EXTENDS);
        $templateChain = $this->cache->get(self::TEMPLATE_WITH_EXTENDS);

        $this->assertSame($lastChild, $templateChain->getLastChild()->getFullyQualifiedName());
    }

    private function installModuleAndReinitializeFixtures(string $moduleId): void
    {
        $this->setupModuleFixture($moduleId);
        $this->setShopSourceFixture();
        $this->setThemeFixture(self::THEME);
        $this->cache->invalidate($this->shopId);
    }

    private function putTemplateChainInCache(string $templateName, TemplateChain $templateChain): void
    {
        $this->cache->put($templateName, $templateChain);
    }

    private function createTemplateChainWithParent(string $templateName, string $moduleId): TemplateChain
    {
        $templateChain = new TemplateChain();
        $templateChain->append(new ModuleTemplateType($templateName, $moduleId));
        $templateChain->append(new ShopTemplateType($templateName));

        return $templateChain;
    }

    private function createTemplateChainWithoutParent(string $templateName): TemplateChain
    {
        $templateChain = new TemplateChain();
        $templateChain->append(new ShopTemplateType($templateName));

        return $templateChain;
    }
}
