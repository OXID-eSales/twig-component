<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\IncludeWidgetLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class IncludeWidgetExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class IncludeWidgetExtension extends AbstractExtension
{
    /**
     * @var IncludeWidgetLogic
     */
    private $includeWidgetLogic;

    /**
     * IncludeWidgetExtension constructor.
     *
     * @param IncludeWidgetLogic $includeWidgetLogic
     */
    public function __construct(IncludeWidgetLogic $includeWidgetLogic)
    {
        $this->includeWidgetLogic = $includeWidgetLogic;
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
