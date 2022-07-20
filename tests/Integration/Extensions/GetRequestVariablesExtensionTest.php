<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Twig\Extensions\GetRequestVariablesExtension;
use Twig\Extension\AbstractExtension;

final class GetRequestVariablesExtensionTest extends AbstractExtensionTest
{
    /** @var GetRequestVariablesExtension */
    protected AbstractExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new GetRequestVariablesExtension();
        $_COOKIE['foo'] = 'bar';
        $_GET['foo'] = 'bar';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_COOKIE['foo']);
        unset($_GET['foo']);
    }

    /**
     * @return array
     */
    public function dummyTemplateProvider(): array
    {
        return [
            ['{{ get_global_cookie("foo") }}', 'bar'],
            ['{{ get_global_cookie("bar") }}', ''],
            ['{{ get_global_get("foo") }}', 'bar'],
            ['{{ get_global_get("bar") }}', ''],
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider dummyTemplateProvider
     */
    public function testIfPhpFunctionsAreCallable(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
