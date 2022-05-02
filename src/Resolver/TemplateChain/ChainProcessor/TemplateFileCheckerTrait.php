<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Webmozart\PathUtil\Path;

trait TemplateFileCheckerTrait
{
    private function templateFileExists(NamespacedDirectory $directory, TemplateTypeInterface $template): bool
    {
        return $this->filesystem->exists(
            Path::join(
                $directory->getDirectory(),
                $template->getRelativeFilePath(),
            )
        );
    }
}
