<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolver;
use OxidEsales\Twig\Resolver\TemplateNameResolver as TwigTemplateNameResolver;
use PHPUnit\Framework\TestCase;

class LegacyTemplateNameResolverTest extends TestCase
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
            new TemplateNameResolver('tpl')
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
            new TemplateNameResolver('html.twig')
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
}
