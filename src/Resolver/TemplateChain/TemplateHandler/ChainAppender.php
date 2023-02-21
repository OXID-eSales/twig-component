<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ChainAppender implements ChainAppenderInterface
{
    public function __construct(
        private Filesystem $filesystem,
    ) {
    }

    public function addToChain(TemplateChain $templateChain, TemplateTypeInterface $templateType, NamespacedDirectory $directory): TemplateChain
    {
        if ($this->directoryContainsTemplateFile($directory, $templateType)) {
            $templateChain->append($templateType);
        }
        return $templateChain;
    }

    private function directoryContainsTemplateFile(NamespacedDirectory $directory, TemplateTypeInterface $template): bool
    {
        return $this->filesystem->exists(
            Path::join($directory->getDirectory(), $template->getRelativeFilePath())
        );
    }
}
