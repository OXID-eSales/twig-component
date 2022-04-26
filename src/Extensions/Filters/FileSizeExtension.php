<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FileSizeLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileSizeExtension extends AbstractExtension
{
    public function __construct(private FileSizeLogic $fileSizeLogic)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [new TwigFilter('file_size', [$this, 'fileSize'])];
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function fileSize($size): string
    {
        return $this->fileSizeLogic->getFileSize($size);
    }
}
