<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslateExtension extends AbstractExtension
{
    public function __construct(private TranslateFilterLogic $multiLangFilterLogic)
    {
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', [$this, 'translate']),
        ];
    }

    /**
     * @param string $ident
     * @param mixed  $arguments
     *
     * @return string
     */
    public function translate($ident, $arguments = null): string
    {
        return $this->multiLangFilterLogic->multiLang($ident, $arguments);
    }
}
