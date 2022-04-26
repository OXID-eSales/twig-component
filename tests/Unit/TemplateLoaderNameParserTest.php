<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Twig\TemplateLoaderNameParser;
use PHPUnit\Framework\TestCase;

final class TemplateLoaderNameParserTest extends TestCase
{
    private TemplateLoaderNameParser $templateLoaderNameParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->templateLoaderNameParser = new TemplateLoaderNameParser();
    }

    /**
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::isValidName
     * @dataProvider getInvalidNameTests
     * @dataProvider getValidNameTests
     */
    public function testIsValidName(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->isValidName($name), $expected['valid']);
    }

    /**
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getLoaderName
     *
     * @dataProvider getValidNameTests
     */
    public function testGetLoaderName(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getLoaderName($name), $expected['loaderName']);
    }

    /**
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getValue
     *
     * @dataProvider getValidNameTests
     */
    public function testGetValue(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getValue($name), $expected['value']);
    }

    /**
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getParameters
     *
     * @dataProvider getValidNameTests
     */
    public function testGetParameters(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getParameters($name), $expected['parameters']);
    }

    /**
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getKey
     *
     * @dataProvider getValidNameTests
     */
    public function testGetKey(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getKey($name), $expected['key']);
    }

    public function getInvalidNameTests(): array
    {
        $invalidNames = [
            '',
            'foo',
            'foo::bar',
            'foo:bar',
            'foo:bar:foo',
            '?foo',
            '?foo=param&bar=param',
            'foo?bar=param',
            'foo::bar?key=param',
            'foo:bar:foo?key=param'
        ];

        return array_map(
            fn ($name) => [$name, ['valid' => false]],
            $invalidNames
        );
    }

    public function getValidNameTests(): array
    {
        return [
            [
                'foo::bar::xy',
                [
                    'valid' => true,
                    'loaderName' => 'foo',
                    'key' => 'bar',
                    'value' => 'xy',
                    'parameters' => []
                ]
            ],
            [
                'foo::bar::xy?key=param&anotherKey=anotherParam',
                [
                    'valid' => true,
                    'loaderName' => 'foo',
                    'key' => 'bar',
                    'value' => 'xy',
                    'parameters' => [
                        'key' => 'param',
                        'anotherKey' => 'anotherParam'
                    ]
                ]
            ]
        ];
    }
}
