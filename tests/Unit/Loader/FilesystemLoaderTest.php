<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\Twig\Loader\FilesystemLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FilesystemLoaderTest extends TestCase
{
    public function testTemplateNameResolving(): void
    {
        $basePath = __DIR__ . "/Fixtures";
        $nameResolver = $this->prophesize(TemplateNameResolverInterface::class);
        $nameResolver->resolve('nameBeforeResolving')->willReturn('index.html.twig');

        $loader = new FilesystemLoader([$basePath], $nameResolver->reveal());

        $this->assertEquals('index file', $loader->getSourceContext('nameBeforeResolving')->getCode());
    }
}
