<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use OxidEsales\Twig\Loader\FilesystemLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

/**
 * Class FilesystemLoaderTest
 */
class FilesystemLoaderTest extends TestCase
{

    public function testEmptyConstructor()
    {
        $loader = new FilesystemLoader();
        $this->assertEquals([], $loader->getPaths());
    }

    public function testFindTemplateLoadError()
    {
        /** @var TemplateLoaderInterface|MockObject $internalLoader */
        $internalLoader = $this->createMock(TemplateLoaderInterface::class);
        $internalLoader->method('getPath')->willReturn("");

        $loader = new FilesystemLoader([], null, $internalLoader, $internalLoader);

        $this->expectException(LoaderError::class);
        $loader->getSourceContext('foo')->getCode();
    }

    public function testFindTemplateByParentClass()
    {
        $basePath = __DIR__ . "/Fixtures";

        $loader = new FilesystemLoader([$basePath]);

        $this->assertEquals('index file', $loader->getSourceContext('index.html.twig')->getCode());
    }

    public function testFindTemplateByInternalLoader()
    {
        /** @var TemplateLoaderInterface|MockObject $internalLoader */
        $internalLoader = $this->createMock(TemplateLoaderInterface::class);
        $internalLoader->method('getPath')->willReturnArgument(0);

        $loader = new FilesystemLoader([], null, $internalLoader, $internalLoader);

        $templateName = 'internal_index.html.twig';
        $this->assertEquals($templateName, $loader->findTemplate($templateName));
    }
}
