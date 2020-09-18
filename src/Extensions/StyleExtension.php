<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\StyleLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * Class AssignAdvancedExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class StyleExtension extends AbstractExtension
{
    /**
     * @var StyleLogic
     */
    private $styleLogic;

    /**
     * StyleExtension constructor.
     *
     * @param StyleLogic $styleLogic
     */
    public function __construct(StyleLogic $styleLogic)
    {
        $this->styleLogic = $styleLogic;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('style', [$this, 'style'], ['needs_environment' => true, 'is_safe' => ['html']])];
    }

    /**
     * @param Environment $env
     * @param array       $params
     *
     * @return string
     */
    public function style(Environment $env, $params = [])
    {
        $globals = $env->getGlobals();
        $isDynamic = $globals['__oxid_include_dynamic'] ?? false;

        return $this->styleLogic->collectStyleSheets($params, $isDynamic);
    }
}
