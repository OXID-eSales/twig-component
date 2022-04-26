<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Twig\Extensions\Filters\PhpFunctionsExtension;
use OxidEsales\TestingLibrary\UnitTestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class PhpFunctionsExtensionTest extends UnitTestCase
{
    protected PhpFunctionsExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new PhpFunctionsExtension();
    }

    public function dummyTemplateProvider(): array
    {
        return [
            ["{% set foo = 'bar'|parse_url %}{{ foo['path'] }}", 'bar'],
            ["{{ 'Mon, 21 Jan 2019 15:35:00 GMT'|strtotime }}", 1_548_084_900],
            ["{{ {0:0, 1:1}|is_array  }}", true],
            ["{{ 'foo'|is_array  }}", false],
            ["{{ 'discount_categories_ajax'|oxNew is null  }}", false]
        ];
    }

    /**
     * @param mixed $expected
     * @dataProvider dummyTemplateProvider
     */
    public function testIfPhpFunctionsAreCallable(string $template, $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    private function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);
        $twig->addGlobal('date', date_create("2013-03-15"));

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }
}
