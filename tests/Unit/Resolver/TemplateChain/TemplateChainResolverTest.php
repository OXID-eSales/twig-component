<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolver;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeFactoryInterface;
use PHPUnit\Framework\TestCase;

final class TemplateChainResolverTest extends TestCase
{
    public function testGetParentWithMultipleCallsWillUseCachedResult(): void
    {
        $templateName = 'widget/header.html.twig';

        $templateTypeMock = $this->createMock(TemplateTypeInterface::class);
        $parentMock = $this->createConfiguredMock(TemplateTypeInterface::class, [
            'getFullyQualifiedName' => '@my_module/widget/header.html.twig'
        ]);
        $templateChainMock = $this->createConfiguredMock(TemplateChain::class, [
            'getParent' => $parentMock
        ]);

        $templateTypeFactoryMock = $this->createMock(TemplateTypeFactoryInterface::class);
        $templateTypeFactoryMock
            ->expects($this->once())
            ->method('createFromTemplateName')
            ->with($templateName)
            ->willReturn($templateTypeMock);

        $templateChainBuilderMock = $this->createMock(TemplateChainBuilderInterface::class);
        $templateChainBuilderMock
            ->expects($this->once())
            ->method('getChain')
            ->with($templateTypeMock)
            ->willReturn($templateChainMock);

        $templateChainResolver = new TemplateChainResolver($templateChainBuilderMock, $templateTypeFactoryMock);

        $templateChainResolver->getParent($templateName);
        $templateChainResolver->getParent($templateName);
        $templateChainResolver->getParent($templateName);
    }

    public function testGetLastChildWithMultipleCallsWillUseCachedResult(): void
    {
        $templateName = 'widget/header.html.twig';

        $templateTypeMock = $this->createMock(TemplateTypeInterface::class);
        $lastChildMock = $this->createConfiguredMock(TemplateTypeInterface::class, [
            'getFullyQualifiedName' => '@my_module/widget/header.html.twig'
        ]);
        $templateChainMock = $this->createConfiguredMock(TemplateChain::class, [
            'getLastChild' => $lastChildMock
        ]);

        $templateTypeFactoryMock = $this->createMock(TemplateTypeFactoryInterface::class);
        $templateTypeFactoryMock
            ->expects($this->once())
            ->method('createFromTemplateName')
            ->with($templateName)
            ->willReturn($templateTypeMock);

        $templateChainBuilderMock = $this->createMock(TemplateChainBuilderInterface::class);
        $templateChainBuilderMock
            ->expects($this->once())
            ->method('getChain')
            ->with($templateTypeMock)
            ->willReturn($templateChainMock);

        $templateChainResolver = new TemplateChainResolver($templateChainBuilderMock, $templateTypeFactoryMock);

        $templateChainResolver->getLastChild($templateName);
        $templateChainResolver->getLastChild($templateName);
        $templateChainResolver->getLastChild($templateName);
    }
}
