<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;

final class TwigCacheTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testWithDefaultCachingMode(): void
    {
        $cache = $this->get('twig')->getCache(false);

        $this->assertInstanceOf(FilesystemCache::class, $cache);
    }

    public function testWithDisabledTemplateCache(): void
    {
        $this->setDisableTemplateCachingParameter();
        $cache = $this->get('twig')->getCache(false);

        $this->assertInstanceOf(NullCache::class, $cache);
    }

    private function setDisableTemplateCachingParameter(): void
    {
        $this->container = (new TestContainerFactory())->create();
        $this->container->setParameter('oxid_esales.templating.disable_twig_template_caching', true);
        $this->container->compile();
    }
}
