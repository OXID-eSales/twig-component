<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\FormatPriceLogic;
use phpDocumentor\Reflection\Types\Integer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class FormatPriceExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class FormatPriceExtension extends AbstractExtension
{
    /**
     * @var FormatPriceLogic
     */
    private $formatPriceLogic;

    /**
     * FormatPriceExtension constructor.
     *
     * @param FormatPriceLogic $formatPriceLogic
     */
    public function __construct(FormatPriceLogic $formatPriceLogic)
    {
        $this->formatPriceLogic = $formatPriceLogic;
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
