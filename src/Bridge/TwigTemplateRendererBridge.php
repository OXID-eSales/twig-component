<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class TwigTemplateRendererBridge implements TemplateRendererBridgeInterface
{
    public function __construct(private TemplateRendererInterface $renderer)
    {
    }

    /**
     * @return TemplateRendererInterface
     */
    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->renderer;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine)
    {
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
    }
}
