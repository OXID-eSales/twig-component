<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Twig\Extensions\Filters\RegexReplaceExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class RegexReplaceExtensionTest extends \PHPUnit\Framework\TestCase
{
    /** @var RegexReplaceExtension */
    protected $extension;

    public function setUp()
    {
        $this->extension = new RegexReplaceExtension();
    }

    /**
     * @return array
     */
    public function dummyTemplateProvider(): array
    {
        return [
            ["{{ '1-foo-2'|regex_replace('/[0-9]/', 'bar') }}", "bar-foo-bar"],
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

        return $twig->loadTemplate('index');
    }
}
