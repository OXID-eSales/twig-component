<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Twig\Extensions\Filters\PhpFunctionsExtension;
use OxidEsales\TestingLibrary\UnitTestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class PhpFunctionsExtensionTest extends UnitTestCase
{
    /** @var PhpFunctionsExtension */
    protected $extension;

    public function setUp()
    {
        $this->extension = new PhpFunctionsExtension();
    }

    /**
     * @return array
     */
    public function dummyTemplateProvider(): array
    {
        return [
            ["{% set foo = 'bar'|parse_url %}{{ foo['path'] }}", 'bar'],
            ["{{ 'Mon, 21 Jan 2019 15:35:00 GMT'|strtotime }}", 1548084900],
            ["{{ {0:0, 1:1}|is_array  }}", true],
            ["{{ 'foo'|is_array  }}", false],
            ["{{ 'discount_categories_ajax'|oxNew is null  }}", false]
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider dummyTemplateProvider
     */
    public function testIfPhpFunctionsAreCallable(string $template, string $expected)
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @param string $template
     *
     * @return \Twig_Template
     */
    private function getTemplate(string $template): \Twig_Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);
        $twig->addGlobal('date', date_create("2013-03-15"));

        return $twig->loadTemplate('index');
    }
}
