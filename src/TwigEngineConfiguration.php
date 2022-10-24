<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class TwigEngineConfiguration implements TwigEngineConfigurationInterface
{
    public function __construct(
        private ContextInterface $context,
        private TwigContextInterface $twigContext
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
            'cache' => $this->context->getTemplateCacheDirectory(),
        ];
    }
}
