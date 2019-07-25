<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration;

use org\bovigo\vfs\vfsStream;
use OxidEsales\Twig\TwigEngine;
use OxidEsales\Twig\TwigEngineConfigurationInterface;
use Twig\Environment;

class TwigEngineTest extends \PHPUnit\Framework\TestCase
{
    private $templateDir;
    private $templateDirPath;
    private $template;

    protected function setUp()
    {
        parent::setUp();
        $templateDir = vfsStream::setup($this->getTemplateDir());
        $this->template = vfsStream::newFile($this->getTemplateName())->at($templateDir)->setContent("{{ 'twig' }}")->url();
        $this->templateDir = $templateDir;
        $this->templateDirPath = vfsStream::url($this->getTemplateDir());
    }

    public function testExists()
    {
        $engine = $this->getEngine();
        $this->assertTrue($engine->exists($this->getTemplateName()));
        $this->assertFalse($engine->exists('foo'));
    }

    public function testAddGlobal()
    {
        $engine = $this->getEngine();
        $engine->addGlobal('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $engine->getGlobals());
        $this->assertNotEquals(['not_foo' => 'not_bar'], $engine->getGlobals());
    }

    public function testRender()
    {
        $engine = $this->getEngine();
        $this->assertTrue(file_exists($this->template));
        $rendered = $engine->render($this->getTemplateName());
        $this->assertEquals('twig', $rendered);
        $this->assertNotEquals('foo', $rendered);
    }

    public function testRenderFragment()
    {
        $engine = $this->getEngine();
        $rendered = $engine->renderFragment("{{ 'twig' }}", 'ox:testid');
        $this->assertEquals('twig', $rendered);
    }

    private function getEngine($engine_type = 'twig')
    {
        /** @var TwigEngineConfigurationInterface $configuration */
        $configuration = $this->getMockBuilder('OxidEsales\Twig\TwigEngineConfigurationInterface')->getMock();
        $configuration->method('getParameters')->willReturn(['template_dir' => [$this->templateDirPath], 'is_debug' => 'false', 'cache_dir' => 'foo']);

        $loader = new \Twig_Loader_Filesystem($this->templateDirPath);

        $engine = new Environment($loader);

        return new TwigEngine($engine);
    }

    private function getTemplateName()
    {
        return 'testTwigTemplate.twig';
    }

    private function getTemplateDir()
    {
        return 'testTemplateDir';
    }

}
