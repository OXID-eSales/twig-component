<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Symfony\Component\Filesystem\Filesystem;

class ShopTemplateProcessor implements ChainProcessorInterface
{
    use TemplateFileCheckerTrait;

    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function process(array $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): array
    {
        if ($this->templateFileExists($directory, $templateType)) {
            $templateChain[] = $templateType->getRelativeFilePath();
        }
        return $templateChain;
    }
}
