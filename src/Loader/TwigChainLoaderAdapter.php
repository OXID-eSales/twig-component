<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use Twig\Loader\ChainLoader;

class TwigChainLoaderAdapter implements TemplateLoaderInterface
{
    /**
     * @var ChainLoader
     */
    private $chain;

    public function __construct(ChainLoader $chain)
    {
        $this->chain = $chain;
    }

    public function exists($name): bool
    {
        return $this->chain->exists($name);
    }

    public function getContext($name): string
    {
        return $this->chain->getSourceContext($name);
    }
}
