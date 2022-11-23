<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\InsertNewBasketItemLogicTwig;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class InsertNewBasketItemExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private InsertNewBasketItemLogicTwig $newBasketItemLogic)
    {
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

    public function getGlobals(): array
    {
        if ($this->newBasketItemLogic instanceof GlobalsInterface) {
            return $this->newBasketItemLogic->getGlobals();
        } else {
            return [];
        }
    }
}
