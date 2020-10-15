<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use Twig\Loader\FilesystemLoader;

class TemplatePathConverter implements TemplatePathConverterInterface
{
    private const NS_MARKER = '@';
    private const EXTENDS_PATH = 'extensions';

    /** @inheritDoc */
    public function trimNamespaceAndExtends(string $path): string
    {
        $pattern = sprintf('/^.*\/?%s\/\w*\/(.*)$/', self::EXTENDS_PATH);
        preg_match($pattern, $path, $match);
        return $match[1];
    }

    /** @inheritDoc */
    public function fillNamespace(string $path): string
    {
        if ($this->hasNamespace($path)) {
            return $path;
        }
        return sprintf('@%s/%s', FilesystemLoader::MAIN_NAMESPACE, $path);
    }

    /** @inheritDoc */
    public function hasNamespace(string $name): bool
    {
        return $name[0] === self::NS_MARKER;
    }

    /** @inheritDoc */
    public function getNamespace(string $path): string
    {
        $parts = explode('/', $path);
        return ltrim($parts[0], self::NS_MARKER);
    }

    public function extendsNamespace(string $path): bool
    {
        return strpos($path, 'extensions/shop/') !== false;
    }

    public function getExtendedNamespace(string $path): string
    {
        $pattern = sprintf('/^.*\/?%s\/(\w+)\/.*$/', self::EXTENDS_PATH);
        preg_match($pattern, $path, $match);
        return $match[1];
    }
}
