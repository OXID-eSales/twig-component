<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

interface TwigContextInterface
{
    /**
     * @deprecated will be removed in v2.0.
     * @return array
     */
    public function getTemplateDirectories(): array;

    /**
     * @return boolean
     */
    public function getIsDebug(): bool;

    /**
     * @deprecated will be removed in v2.0.
     * Use \OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface::getTemplateCacheDirectory()
     * @return string
     */
    public function getCacheDir(): string;
}
