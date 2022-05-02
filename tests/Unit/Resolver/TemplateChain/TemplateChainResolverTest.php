<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolver;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class TemplateChainResolverTest extends TestCase
{
    use ProphecyTrait;

    private string $previousTemplate = 'some-previous-template';
    private string $someTestedTemplate = 'some-template';
    private string $someResolvedTemplate = 'some-resolved-template';
    private string $nextTemplate = 'some-next-template';
    private ObjectProphecy|TemplateChainBuilderInterface $templateChain;
    private ObjectProphecy|TemplateFileResolverInterface $templateFileResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateChain = $this->prophesize(TemplateChainBuilderInterface::class);
        $this->templateFileResolver = $this->prophesize(TemplateFileResolverInterface::class);
    }

    public function testGetParent(): void
    {
        $this->templateFileResolver->getFilename($this->someTestedTemplate)->willReturn($this->someResolvedTemplate);
        $this->templateChain->getChain($this->someResolvedTemplate)->willReturn(
            [
                $this->previousTemplate,
                $this->someResolvedTemplate,
                $this->nextTemplate,
            ]
        );

        $parent = $this->getTemplateChainResolver()->getParent($this->someTestedTemplate);

        $this->assertSame($this->nextTemplate, $parent);
    }

    public function testGetLastChild(): void
    {
        $this->templateFileResolver->getFilename($this->someTestedTemplate)->willReturn($this->someResolvedTemplate);
        $this->templateChain->getChain($this->someResolvedTemplate)->willReturn(
            [
                $this->previousTemplate,
                $this->someResolvedTemplate,
                $this->nextTemplate,
            ]
        );

        $parent = $this->getTemplateChainResolver()->getLastChild($this->someTestedTemplate);

        $this->assertSame($this->previousTemplate, $parent);
    }

    public function testHasParentWithParentPresent(): void
    {
        $this->templateFileResolver->getFilename($this->someTestedTemplate)->willReturn($this->someResolvedTemplate);
        $this->templateChain->getChain($this->someResolvedTemplate)->willReturn(
            [
                $this->previousTemplate,
                $this->someResolvedTemplate,
                $this->nextTemplate,
            ]
        );

        $actual = $this->getTemplateChainResolver()->hasParent($this->someTestedTemplate);

        $this->assertTrue($actual);
    }

    public function testHasParentWithLastInChain(): void
    {
        $this->templateFileResolver->getFilename($this->someTestedTemplate)->willReturn($this->someResolvedTemplate);
        $this->templateChain->getChain($this->someResolvedTemplate)->willReturn(
            [
                $this->previousTemplate,
                $this->someResolvedTemplate,
            ]
        );

        $actual = $this->getTemplateChainResolver()->hasParent($this->someTestedTemplate);

        $this->assertFalse($actual);
    }

    private function getTemplateChainResolver(): TemplateChainResolverInterface
    {
        return new TemplateChainResolver(
            $this->templateChain->reveal(),
            $this->templateFileResolver->reveal()
        );
    }
}
