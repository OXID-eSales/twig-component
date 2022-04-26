<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatPriceLogic;
use phpDocumentor\Reflection\Types\Integer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormatPriceExtension extends AbstractExtension
{
    public function __construct(private FormatPriceLogic $formatPriceLogic)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_price', [$this, 'formatPrice'])
        ];
    }

    /**
     * @param mixed $price
     * @param array $params
     *
     * @return string
     */
    public function formatPrice($price, array $params = []): string
    {
        $params['price'] = $price;

        return $this->formatPriceLogic->formatPrice($params);
    }
}
