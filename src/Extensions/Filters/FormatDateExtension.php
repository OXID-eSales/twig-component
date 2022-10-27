<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FormatDateLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatDateExtension extends AbstractExtension
{
    public function __construct(private FormatDateLogic $formatDateLogic)
    {
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_date', [$this, 'formatDate']),
        ];
    }

    /**
     * @param object $convObject
     * @param string|null $fieldType
     * @param bool $passedValue
     *
     * @return string
     */
    public function formatDate($convObject, string $fieldType = null, bool $passedValue = false): ?string
    {
        return $this->formatDateLogic->formdate($convObject, $fieldType, $passedValue);
    }
}
