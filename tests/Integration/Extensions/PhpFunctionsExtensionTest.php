<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Twig\Extensions\PhpFunctionsExtension;
use PHPUnit\Framework\Attributes\DataProvider;

final class PhpFunctionsExtensionTest extends AbstractExtensionTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new PhpFunctionsExtension();
    }

    public static function dummyTemplateProvider(): array
    {
        return [
            ['{{ count({0:0, 1:1, 2:2}) }}', 3],
            ['{{ empty({0:0, 1:1}) }}', false],
            ['{{ empty({}) }}', true],
            ['{{ empty() }}', true],
            ['{{ isset(foo) }}', false],
            ["{% set foo = 'bar' %} {{ isset(foo) }}", true],
        ];
    }

    #[DataProvider('dummyTemplateProvider')]
    public function testIfPhpFunctionsAreCallable(string $template, bool|int $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
