<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Twig\Extensions\Filters\RegexReplaceExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class RegexReplaceExtensionTest extends TestCase
{
    protected RegexReplaceExtension $extension;

    public function setUp(): void
    {
        $this->extension = new RegexReplaceExtension();
    }

    public static function dummyTemplateProvider(): array
    {
        return [
            ["{{ '1-foo-2'|regex_replace('/[0-9]/', 'bar') }}", 'bar-foo-bar'],
        ];
    }

    /**
     * @dataProvider dummyTemplateProvider
     */
    public function testIfPhpFunctionsAreCallable(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    private function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }
}
