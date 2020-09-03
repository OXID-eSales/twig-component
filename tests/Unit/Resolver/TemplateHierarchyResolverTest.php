<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use OxidEsales\Twig\Resolver\ModuleTemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateHierarchyResolver;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

final class TemplateHierarchyResolverTest extends TestCase
{
    private $ancestorTemplateName = 'ancestorTemplateName';

    public function testReturnsAncestorWithEmptyChain(): void
    {
        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock([]),
            $this->getTemplateNameResolverMock()
        );

        $this->assertEquals(
            $this->ancestorTemplateName,
            $templateHierarchyResolver->getParentForTemplate('someTemplate', $this->ancestorTemplateName)
        );
    }

    public function testReturnsAncestorWithTemplateLastInHierarchy(): void
    {
        $templateWithMainNamespace = '@' . FilesystemLoader::MAIN_NAMESPACE . '/someTemplate';

        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock(['@someModule/someTemplate']),
            $this->getTemplateNameResolverMock()
        );

        $this->assertEquals(
            $this->ancestorTemplateName,
            $templateHierarchyResolver->getParentForTemplate(
                $templateWithMainNamespace,
                $this->ancestorTemplateName
            )
        );
    }

    public function testReturnsSecondParrentModuleTemplateWithTemplateWithoutNamespace(): void
    {
        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock([
                '@firstModule/someTemplate',
                '@secondModule/someTemplate',
            ]),
            $this->getTemplateNameResolverMock()
        );

        $this->assertEquals(
            '@secondModule/someTemplate',
            $templateHierarchyResolver->getParentForTemplate(
                'someTemplate',
                $this->ancestorTemplateName
            )
        );
    }

    public function testReturnsNextParrentModuleTemplateWithTemplateWithNamespace(): void
    {
        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock([
                '@firstModule/someTemplate',
                '@secondModule/someTemplate',
                '@thirdModule/someTemplate',
            ]),
            $this->getTemplateNameResolverMock()
        );

        $this->assertEquals(
            '@thirdModule/someTemplate',
            $templateHierarchyResolver->getParentForTemplate(
                '@secondModule/someTemplate',
                $this->ancestorTemplateName
            )
        );
    }

    public function testReturnsTemplateWithMainNamespaceAtTheEndOfHierarchy(): void
    {
        $templateWithMainNamespace = '@' . FilesystemLoader::MAIN_NAMESPACE . '/someTemplate';

        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock([
                '@firstModule/someTemplate',
                '@secondModule/someTemplate',
                '@thirdModule/someTemplate',
            ]),
            $this->getTemplateNameResolverMock()
        );

        $this->assertEquals(
            $templateWithMainNamespace,
            $templateHierarchyResolver->getParentForTemplate(
                '@thirdModule/someTemplate',
                $this->ancestorTemplateName
            )
        );
    }

    public function testTemplateNameResolving(): void
    {
        $templateNameResolver = $this->getMockBuilder(TemplateNameResolverInterface::class)->getMock();
        $templateNameResolver->method('resolve')->willReturn('@firstModule/someTemplate');

        $templateHierarchyResolver = new TemplateHierarchyResolver(
            $this->getChainResolverMock([
                '@firstModule/someTemplate',
                '@secondModule/someTemplate',
            ]),
            $templateNameResolver
        );

        $this->assertEquals(
            '@secondModule/someTemplate',
            $templateHierarchyResolver->getParentForTemplate(
                'someNameBeforeResolving',
                $this->ancestorTemplateName
            )
        );
    }

    private function getChainResolverMock(array $chain): ModuleTemplateChainResolverInterface
    {
        $mock = $this->getMockBuilder(ModuleTemplateChainResolverInterface::class)->getMock();
        $mock->method('getChain')->with('someTemplate')->willReturn($chain);
        return $mock;
    }

    private function getTemplateNameResolverMock(): TemplateNameResolverInterface
    {
        $mock = $this->getMockBuilder(TemplateNameResolverInterface::class)->getMock();
        $mock->method('resolve')->willReturnArgument(0);
        return $mock;
    }
}
