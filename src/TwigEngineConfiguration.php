<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class TwigEngineConfiguration implements TwigEngineConfigurationInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly TwigContextInterface $twigContext,
        private readonly ShopTemplateCacheServiceInterface $shopTemplateCacheService,
        private readonly bool $disableTemplateCaching,
    ) {
    }

    /**
     * Return an array of twig parameters to configure.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'debug' => $this->twigContext->getIsDebug(),
            'cache' => $this->disableTemplateCaching
                ? false
                : $this->shopTemplateCacheService->getCacheDirectory($this->context->getCurrentShopId()),
        ];
    }
}
