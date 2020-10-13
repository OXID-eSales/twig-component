<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

interface ModulesTemplateDirectoryResolverInterface
{
    public function getAbsolutePath(string $moduleId): string;
}
