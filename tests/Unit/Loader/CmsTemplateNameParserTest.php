<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Loader;

use OxidEsales\Twig\Loader\CmsTemplateNameParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CmsTemplateNameParserTest extends TestCase
{
    private CmsTemplateNameParser $cmsTemplateNameParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cmsTemplateNameParser = new CmsTemplateNameParser();
    }

    #[DataProvider('getValidNameTests')]
    #[DataProvider('getInvalidNameTests')]
    public function testIsValidName(string $name, array $expected): void
    {
        $this->assertEquals($this->cmsTemplateNameParser->isValidName($name), $expected['valid']);
    }

    #[DataProvider('getValidNameTests')]
    public function testGetLoaderName(string $name, array $expected): void
    {
        $this->assertEquals($this->cmsTemplateNameParser->getLoaderName($name), $expected['loaderName']);
    }

    #[DataProvider('getValidNameTests')]
    public function testGetValue(string $name, array $expected): void
    {
        $this->assertEquals($this->cmsTemplateNameParser->getValue($name), $expected['value']);
    }

    #[DataProvider('getValidNameTests')]
    public function testGetParameters(string $name, array $expected): void
    {
        $this->assertEquals($this->cmsTemplateNameParser->getParameters($name), $expected['parameters']);
    }

    #[DataProvider('getValidNameTests')]
    public function testGetKey(string $name, array $expected): void
    {
        $this->assertEquals($this->cmsTemplateNameParser->getKey($name), $expected['key']);
    }

    public static function getInvalidNameTests(): array
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
            static fn ($name) => [$name, ['valid' => false]],
            $invalidNames
        );
    }

    public static function getValidNameTests(): array
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
