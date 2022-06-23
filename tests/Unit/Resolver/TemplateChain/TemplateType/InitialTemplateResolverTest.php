<?php

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\InitialTemplateResolver;
use PHPUnit\Framework\TestCase;

class InitialTemplateResolverTest extends TestCase
{
    public function testGetTemplateOriginWithShopTemplate(): void
    {
        $name = 'template-name';
        $expected = new ShopTemplateType($name);
        $extensionTemplate = $expected;
        $initialTemplate = (new InitialTemplateResolver())->getInitialTemplate($extensionTemplate);

        $this->assertInstanceOf(ShopTemplateType::class, $initialTemplate);
        $this->assertEquals($expected, $initialTemplate);
    }

    public function testGetTemplateOriginWithShopExtensionTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $theme = 'theme-id';
        $extensionTemplate = new ShopExtensionTemplateType($name, $namespace, $theme);
        $expected = new ShopTemplateType($name);
        $initialTemplate = (new InitialTemplateResolver())->getInitialTemplate($extensionTemplate);

        $this->assertInstanceOf(ShopTemplateType::class, $initialTemplate);
        $this->assertEquals($expected, $initialTemplate);
    }

    public function testGetTemplateOriginWithModuleTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $expected = new ModuleTemplateType($name, $namespace);
        $extensionTemplate = $expected;
        $initialTemplate = (new InitialTemplateResolver())->getInitialTemplate($extensionTemplate);

        $this->assertInstanceOf(ModuleTemplateType::class, $initialTemplate);
        $this->assertEquals($expected, $initialTemplate);
    }

    public function testGetTemplateOriginWithModuleExtensionTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $parentNamespace = 'parent-module-id';
        $extensionTemplate = new ModuleExtensionTemplateType($name, $namespace, $parentNamespace);
        $expected = new ModuleTemplateType($name, $parentNamespace);
        $initialTemplate = (new InitialTemplateResolver())->getInitialTemplate($extensionTemplate);

        $this->assertInstanceOf(ModuleTemplateType::class, $initialTemplate);
        $this->assertEquals($expected, $initialTemplate);
    }
}
