<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use phpDocumentor\Reflection\Types\Mixed_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TranslateExtension extends AbstractExtension
{
    public function __construct(private TranslateFunctionLogic $translateFunctionLogic)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('translate', [$this, 'translate'])];
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
