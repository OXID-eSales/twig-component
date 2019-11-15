<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolver;
use OxidEsales\Twig\Resolver\TemplateNameResolver as TwigTemplateNameResolver;
use PHPUnit\Framework\TestCase;

class TemplateNameResolverTest extends TestCase
{
    /**
     * @param string $templateName
     * @param string $response
     *
     * @dataProvider resolveSmartyDataProvider
     */
    public function testResolveSmartyTemplate(string $templateName, string $response): void
    {
        $resolver = new TwigTemplateNameResolver(
            new TemplateNameResolver(
                $this->getTemplateEngineMock('tpl')
            )
        );

        $this->assertSame($response, $resolver->resolve($templateName));
    }

    /**
     * @return array
     */
    public function resolveSmartyDataProvider(): array
    {
        return [
            [
                'template.tpl',
                'template.tpl'
            ],
            [
                'some/path/template.tpl',
                'some/path/template.tpl'
            ],
            [
                'some/path/template_name.tpl',
                'some/path/template_name.tpl'
            ],
            [
                'some/path/template.name.tpl',
                'some/path/template.name.tpl'
            ],
            [
                'template.html.twig',
                'template.tpl'
            ],
            [
                'some/path/template.html.twig',
                'some/path/template.tpl'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.tpl'
            ],
            [
                'some/path/template.name.html.twig',
                'some/path/template.name.tpl'
            ],
            [
                '',
                ''
            ]
        ];
    }

    /**
     * @param string $templateName
     * @param string $response
     *
     * @dataProvider resolveTwigDataProvider
     */
    public function testResolveTwigTemplate(string $templateName, string $response): void
    {
        $resolver = new TwigTemplateNameResolver(
            new TemplateNameResolver(
                $this->getTemplateEngineMock('html.twig')
            )
        );

        $this->assertSame($response, $resolver->resolve($templateName));
    }

    /**
     * @return array
     */
    public function resolveTwigDataProvider(): array
    {
        return [
            [
                'template.tpl',
                'template.html.twig'
            ],
            [
                'some/path/template_name.tpl',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.tpl',
                'some/path/template.name.html.twig'
            ],
            [
                'template.html.twig',
                'template.html.twig'
            ],
            [
                'some/path/template_name.html.twig',
                'some/path/template_name.html.twig'
            ],
            [
                'some/path/template.name.html.twig',
                'some/path/template.name.html.twig'
            ],
            [
                '',
                ''
            ]
        ];
    }

    /**
     * @param string $extension
     *
     * @return TemplateEngineInterface
     */
    private function getTemplateEngineMock(string $extension): TemplateEngineInterface
    {
        $engine = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $engine->expects($this->any())
            ->method('getDefaultFileExtension')
            ->will($this->returnValue($extension));

        return $engine;
    }
}
