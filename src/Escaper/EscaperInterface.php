<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Escaper;

interface EscaperInterface
{
    /**
     * @return string
     */
    public function getStrategy(): string;

    /**
     * Escape a string for the given strategy.
     * Matches Twig 3.10+ escaper callable signature (no Environment argument).
     */
    public function escape(string $string, string $charset): string;
}
