<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

class TwigEngineConfiguration implements TwigEngineConfigurationInterface
{
    public function __construct(private TwigContextInterface $context)
    {
    }

    /**
     * Return an array of twig parameters to configure.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'debug' => $this->context->getIsDebug(),
            'cache' => $this->context->getCacheDir(),
        ];
    }
}
