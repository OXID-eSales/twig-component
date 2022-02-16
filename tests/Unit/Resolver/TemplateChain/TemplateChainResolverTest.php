<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolver;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class TemplateChainResolverTest extends TestCase
{
    private $previousTemplate = 'some-previous-template';
    private $someTestedTemplate = 'some-template';
    private $someResolvedTemplate = 'some-resolved-template';
    private $nextTemplate = 'some-next-template';
    /** @var TemplateChainInterface|ObjectProphecy */
    private $templateChain;
    /** @var TemplateFileResolverInterface|ObjectProphecy */
    private $templateFileResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateChain = $this->prophesize(TemplateChainInterface::class);
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
