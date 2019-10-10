<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use phpDocumentor\Reflection\Types\Mixed_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TranslateExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class TranslateExtension extends AbstractExtension
{
    /**
     * @var TranslateFunctionLogic
     */
    private $translateFunctionLogic;

    /**
     * TranslateExtension constructor.
     *
     * @param TranslateFunctionLogic $translateFunctionLogic
     */
    public function __construct(TranslateFunctionLogic $translateFunctionLogic)
    {
        $this->translateFunctionLogic = $translateFunctionLogic;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('translate', [$this, 'translate'], ['is_safe' => ['html']])];
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function translate(array $params): string
    {
        return $this->translateFunctionLogic->getTranslation($params);
    }
}
