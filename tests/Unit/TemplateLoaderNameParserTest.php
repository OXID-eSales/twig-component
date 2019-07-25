<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit;

use OxidEsales\Twig\TemplateLoaderNameParser;
use PHPUnit\Framework\TestCase;

/**
 * Class TemplateLoaderNameParserTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class TemplateLoaderNameParserTest extends TestCase
{

    /**
     * @var TemplateLoaderNameParser
     */
    private $templateLoaderNameParser;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->templateLoaderNameParser = new TemplateLoaderNameParser();
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::isValidName
     *
     * @dataProvider getInvalidNameTests
     * @dataProvider getValidNameTests
     */
    public function testIsValidName(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->isValidName($name), $expected['valid']);
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getLoaderName
     *
     * @dataProvider getValidNameTests
     */
    public function testGetLoaderName(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getLoaderName($name), $expected['loaderName']);
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getValue
     *
     * @dataProvider getValidNameTests
     */
    public function testGetValue(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getValue($name), $expected['value']);
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getParameters
     *
     * @dataProvider getValidNameTests
     */
    public function testGetParameters(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getParameters($name), $expected['parameters']);
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @covers \OxidEsales\Twig\TemplateLoaderNameParser::getKey
     *
     * @dataProvider getValidNameTests
     */
    public function testGetKey(string $name, array $expected): void
    {
        $this->assertEquals($this->templateLoaderNameParser->getKey($name), $expected['key']);
    }

    /**
     * @return array
     */
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
            function ($name) {
                return [$name, ['valid' => false]];
            },
            $invalidNames
        );
    }

    /**
     * @return array
     */
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
