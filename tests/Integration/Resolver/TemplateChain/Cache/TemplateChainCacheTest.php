<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Resolver\TemplateChain\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCache;
use OxidEsales\Twig\Resolver\TemplateChain\Cache\TemplateChainCacheNotFoundException;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use PHPUnit\Framework\TestCase;

final class TemplateChainCacheTest extends TestCase
{
    use ContainerTrait;

    private const MODULE_ID = 'module1';
    private const TEMPLATE_NAME = 'template.html.twig';
    private const OTHER_SHOP_ID = 999;

    private TemplateChainCache $cache;
    private int $shopId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new TemplateChainCache($this->get(ContextInterface::class));
        $this->shopId = $this->get(BasicContextInterface::class)->getDefaultShopId();
    }

    protected function tearDown(): void
    {
        $this->cache->invalidate($this->shopId);
        $this->cache->invalidate(self::OTHER_SHOP_ID);

        parent::tearDown();
    }

    public function testGetReturnsPersistedTemplateChainWithParent(): void
    {
        $this->cache->put(
            self::TEMPLATE_NAME,
            $this->createTemplateChainWithParent()
        );

        $reloadedCache = new TemplateChainCache($this->get(ContextInterface::class));
        $templateChain = $reloadedCache->get(self::TEMPLATE_NAME);

        $this->assertSame('@' . self::MODULE_ID . '/template.html.twig', $templateChain->getLastChild()->getFullyQualifiedName());
        $this->assertTrue($templateChain->hasParent(new ModuleTemplateType(self::TEMPLATE_NAME, self::MODULE_ID)));
    }

    public function testInvalidateRemovesOnlyCacheEntriesForTheGivenShopId(): void
    {
        $this->cache->put(
            self::TEMPLATE_NAME,
            $this->createTemplateChainWithParent()
        );

        $otherShopContext = new ContextStub();
        $otherShopContext->setCurrentShopId(self::OTHER_SHOP_ID);
        $otherShopCache = new TemplateChainCache($otherShopContext);
        $otherShopCache->put(self::TEMPLATE_NAME, $this->createTemplateChainWithoutParent());

        $this->cache->invalidate($this->shopId);

        $this->assertSame(
            '@__main__/template.html.twig',
            $otherShopCache->get(self::TEMPLATE_NAME)->getLastChild()->getFullyQualifiedName()
        );

        $this->expectException(TemplateChainCacheNotFoundException::class);
        $this->cache->get(self::TEMPLATE_NAME);
    }

    public function testGetThrowsExceptionWhenCacheDoesNotExist(): void
    {
        $this->expectException(TemplateChainCacheNotFoundException::class);

        $this->cache->get(self::TEMPLATE_NAME);
    }

    private function createTemplateChainWithParent(): TemplateChain
    {
        $templateChain = new TemplateChain();
        $templateChain->append(new ModuleTemplateType(self::TEMPLATE_NAME, self::MODULE_ID));
        $templateChain->append(new ShopTemplateType(self::TEMPLATE_NAME));

        return $templateChain;
    }

    private function createTemplateChainWithoutParent(): TemplateChain
    {
        $templateChain = new TemplateChain();
        $templateChain->append(new ShopTemplateType(self::TEMPLATE_NAME));

        return $templateChain;
    }
}
