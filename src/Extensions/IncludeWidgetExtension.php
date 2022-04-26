<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeWidgetLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IncludeWidgetExtension extends AbstractExtension
{
    public function __construct(private IncludeWidgetLogic $includeWidgetLogic)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('include_widget', [$this, 'includeWidget'], ['is_safe' => ['html']])];
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function includeWidget($params)
    {
        return $this->includeWidgetLogic->renderWidget($params);
    }
}
