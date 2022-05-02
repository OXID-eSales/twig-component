<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;

interface TemplateDirectoryResolverInterface
{
    /**
     * @return NamespacedDirectory[]
     */
    public function getTemplateDirectories(): array;
}
