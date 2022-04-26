<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

interface TwigContextInterface
{
    /**
     * @return array
     */
    public function getTemplateDirectories(): array;

    /**
     * @return boolean
     */
    public function getIsDebug(): bool;

    /**
     * @return string
     */
    public function getCacheDir(): string;

    /**
     * @return string
     */
    public function getActiveThemeId(): string;
}
