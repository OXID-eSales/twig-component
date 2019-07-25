<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\FileSizeLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class FileSizeExtension
 *
 * @package OxidEsales\Twig\Filters
 * @author  Jędrzej Skoczek
 */
class FileSizeExtension extends AbstractExtension
{
    /**
     * @var FileSizeLogic
     */
    private $fileSizeLogic;

    /**
     * FileSizeExtension constructor.
     *
     * @param FileSizeLogic $fileSizeLogic
     */
    public function __construct(FileSizeLogic $fileSizeLogic)
    {
        $this->fileSizeLogic = $fileSizeLogic;
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
