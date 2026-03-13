<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Path;

use function sha1;

final class TemplateChainCache implements TemplateChainCacheInterface
{
    private array $memoryCache = [];
    private array $cachePools = [];

    public function __construct(
        private readonly ContextInterface $context,
    ) {
    }

    public function put(string $templateName, TemplateChain $templateChain): void
    {
        $shopId = $this->context->getCurrentShopId();
        $this->memoryCache[$shopId][$templateName] = $templateChain;

        $cacheItem = $this->getCache($shopId)->getItem($this->getCacheKey($templateName));
        $cacheItem->set($templateChain);
        $this->getCache($shopId)->save($cacheItem);
    }

    public function get(string $templateName): TemplateChain
    {
        $shopId = $this->context->getCurrentShopId();

        if (!isset($this->memoryCache[$shopId][$templateName])) {
            $cacheItem = $this->getCache($shopId)->getItem($this->getCacheKey($templateName));

            if (!$cacheItem->isHit()) {
                throw new TemplateChainCacheNotFoundException(
                    "Template chain cache with template '$templateName' for shop id $shopId not found."
                );
            }

            $this->memoryCache[$shopId][$templateName] = $cacheItem->get();
        }

        return $this->memoryCache[$shopId][$templateName];
    }

    public function invalidate(int $shopId): void
    {
        unset($this->memoryCache[$shopId]);
        $this->getCache($shopId)->clear();
    }

    private function getCache(int $shopId): FilesystemAdapter
    {
        if (!isset($this->cachePools[$shopId])) {
            $this->cachePools[$shopId] = new FilesystemAdapter(
                directory: $this->getCacheDirectory($shopId),
            );
        }

        return $this->cachePools[$shopId];
    }

    private function getCacheDirectory(int $shopId): string
    {
        return Path::join(
            $this->context->getCacheDirectory(),
            'twig_component',
            'template_chain',
            (string) $shopId
        );
    }

    private function getCacheKey(string $templateName): string
    {
        return sha1($templateName);
    }
}
