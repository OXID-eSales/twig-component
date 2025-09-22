<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Framework\Html\HtmlSanitizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SanitizeHtmlExtension extends AbstractExtension
{
    public function __construct(
        private readonly HtmlSanitizerInterface $sanitizer,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'sanitize_html',
                $this->sanitize(...),
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function sanitize(string $html): string
    {
        return $this->sanitizer->sanitize($html);
    }
}
