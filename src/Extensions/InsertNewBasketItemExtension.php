<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\InsertNewBasketItemLogicTwig;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class InsertNewBasketItemExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class InsertNewBasketItemExtension extends AbstractExtension
{
    /**
     * @var InsertNewBasketItemLogicTwig
     */
    private $newBasketItemLogic;

    /**
     * InputHelpExtension constructor.
     *
     * @param InsertNewBasketItemLogicTwig $newBasketItemLogic
     */
    public function __construct(InsertNewBasketItemLogicTwig $newBasketItemLogic)
    {
        $this->newBasketItemLogic = $newBasketItemLogic;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [new TwigFunction('insert_new_basket_item', [$this, 'insertNewBasketItem'], ['needs_environment' => true])];
    }

    /**
     * @param Environment $env
     * @param array       $params
     *
     * @return string
     */
    public function insertNewBasketItem(Environment $env, $params): string
    {
        return $this->newBasketItemLogic->getNewBasketItemTemplate($params, $env);
    }
}
