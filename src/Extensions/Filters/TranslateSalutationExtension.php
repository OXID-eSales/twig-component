<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\TranslateSalutationLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class TranslateSalutationExtension
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class TranslateSalutationExtension extends AbstractExtension
{
    /**
     * @var TranslateSalutationLogic
     */
    private $translateSalutationLogic;

    /**
     * TranslateSalutationExtension constructor.
     *
     * @param TranslateSalutationLogic $translateSalutationLogic
     */
    public function __construct(TranslateSalutationLogic $translateSalutationLogic)
    {
        $this->translateSalutationLogic = $translateSalutationLogic;
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
     * @param string $ident
     *
     * @return string
     */
    public function translateSalutation(string $ident = null): string
    {
        return $this->translateSalutationLogic->translateSalutation($ident);
    }
}
