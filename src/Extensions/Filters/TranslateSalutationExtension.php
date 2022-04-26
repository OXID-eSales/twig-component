<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateSalutationLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslateSalutationExtension extends AbstractExtension
{
    public function __construct(private TranslateSalutationLogic $translateSalutationLogic)
    {
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate_salutation', [$this, 'translateSalutation'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string|null $ident
     *
     * @return string
     */
    public function translateSalutation(string $ident = null): string
    {
        return $this->translateSalutationLogic->translateSalutation($ident);
    }
}
