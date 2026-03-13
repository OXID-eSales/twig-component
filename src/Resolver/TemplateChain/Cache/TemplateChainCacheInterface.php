<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\Cache;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;

interface TemplateChainCacheInterface
{
    public function put(string $templateName, TemplateChain $templateChain): void;

    /**
     * @throws TemplateChainCacheNotFoundException
     */
    public function get(string $templateName): TemplateChain;

    public function invalidate(int $shopId): void;
}
