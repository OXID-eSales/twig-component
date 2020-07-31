<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use Twig\Loader\FilesystemLoader;

class TemplateNameConverter implements TemplateNameConverterInterface
{
    /** @inheritDoc */
    public function trimNamespace(string $name): string
    {
        if (!$this->hasNamespace($name)) {
            return $name;
        }
        $parts = explode('/', $name);
        unset($parts[0]);
        return implode('/', $parts);
    }

    /** @inheritDoc */
    public function fillNamespace(string $name): string
    {
        if ($this->hasNamespace($name)) {
            return $name;
        }
        return sprintf('@%s/%s', FilesystemLoader::MAIN_NAMESPACE, $name);
    }

    private function hasNamespace(string $name): bool
    {
        return $name[0] === '@';
    }
}
