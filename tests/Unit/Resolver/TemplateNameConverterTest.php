<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\Twig\Resolver\TemplateNameConverter;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

final class TemplateNameConverterTest extends TestCase
{
    public function testTrimNamespaceWithoutNamespace(): void
    {
        $templateName = 'abc';

        $actual = (new TemplateNameConverter())->trimNamespace($templateName);

        $this->assertSame($templateName, $actual);
    }

    public function testTrimNamespaceWithNamespace(): void
    {
        $templateName = 'abc/def';
        $namespace = '@namespace';

        $actual = (new TemplateNameConverter())->trimNamespace("$namespace/$templateName");

        $this->assertSame($templateName, $actual);
    }

    public function testFillNamespaceWithoutNamespace(): void
    {
        $templateName = 'abc';
        $mainNamespace = '@' . FilesystemLoader::MAIN_NAMESPACE;

        $actual = (new TemplateNameConverter())->fillNamespace($templateName);

        $this->assertSame("$mainNamespace/$templateName", $actual);
    }

    public function testFillNamespaceWithNamespace(): void
    {
        $templateName = 'abc';
        $namespace = '@namespace';

        $actual = (new TemplateNameConverter())->fillNamespace("$namespace/$templateName");

        $this->assertSame("$namespace/$templateName", $actual);
    }
}
