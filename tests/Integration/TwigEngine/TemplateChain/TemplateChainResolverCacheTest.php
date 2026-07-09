<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\TwigEngine\TemplateChain;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Tests\Integration\TestingFixturesTrait;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class TemplateChainResolverCacheTest extends IntegrationTestCase
{
    use ContainerTrait;
    use TestingFixturesTrait;

    private const TEMPLATE_NAME = 'layout/base.html.twig';
    private const TEMPLATE_WITH_MODULE = 'some-template.html.twig';
    private const MODULE_ID = 'testModule';
    private const MODULE_TEMPLATE = '@' . self::MODULE_ID . '/' . self::TEMPLATE_WITH_MODULE;
    private const FIXTURE_THEME = 'apex';

    private TemplateChainResolverInterface $resolver;
    private TagAwareCacheInterface $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->setThemeFixture(self::FIXTURE_THEME);

        $this->resolver = $this->get(TemplateChainResolverInterface::class);
        $this->cache = $this->get(TagAwareCacheInterface::class);
        $this->cache->invalidateTags(['oxid_esales.cache.twig.template_chain']);
    }

    public function tearDown(): void
    {
        $this->cache->invalidateTags(['oxid_esales.cache.twig.template_chain']);

        parent::tearDown();
    }

    public function testGetLastChildPopulatesCacheOnFirstCall(): void
    {
        $cacheKey = $this->getCacheKey(self::TEMPLATE_NAME);

        $this->assertFalse($this->cache->getItem($cacheKey)->isHit());

        $this->resolver->getLastChild(self::TEMPLATE_NAME);

        $this->assertTrue($this->cache->getItem($cacheKey)->isHit());
    }

    public function testGetLastChildReturnsValueFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::TEMPLATE_WITH_MODULE,
            $this->createTemplateChainWithParent(self::TEMPLATE_WITH_MODULE, self::MODULE_ID)
        );

        $this->assertSame(
            self::MODULE_TEMPLATE,
            $this->resolver->getLastChild(self::TEMPLATE_WITH_MODULE)
        );
    }

    public function testGetParentReturnsValueFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::MODULE_TEMPLATE,
            $this->createTemplateChainWithParent(self::TEMPLATE_WITH_MODULE, self::MODULE_ID)
        );

        $this->assertSame(
            '@__main__/' . self::TEMPLATE_WITH_MODULE,
            $this->resolver->getParent(self::MODULE_TEMPLATE)
        );
    }

    public function testHasParentReturnsFalseFromExistingCacheEntry(): void
    {
        $this->putTemplateChainInCache(
            self::TEMPLATE_WITH_MODULE,
            $this->createTemplateChainWithoutParent(self::TEMPLATE_WITH_MODULE)
        );

        $this->assertFalse($this->resolver->hasParent(self::TEMPLATE_WITH_MODULE));
    }

    private function putTemplateChainInCache(string $templateName, TemplateChain $templateChain): void
    {
        $item = $this->cache->getItem($this->getCacheKey($templateName));
        $item->set($templateChain);
        $item->tag('oxid_esales.cache.twig.template_chain');
        $this->cache->save($item);
    }

    private function getCacheKey(string $templateName): string
    {
        return 'twig_template_chain_' . sha1($templateName);
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
