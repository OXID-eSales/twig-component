<?php

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateOriginResolver;
use PHPUnit\Framework\TestCase;

class TemplateOriginResolverTest extends TestCase
{
    public function testGetTemplateOriginWithShopTemplate(): void
    {
        $name = 'template-name';
        $extensionTemplate = $expected = new ShopTemplateType($name);
        $templateOrigin = (new TemplateOriginResolver())->getTemplateOrigin($extensionTemplate);

        $this->assertInstanceOf(ShopTemplateType::class, $templateOrigin);
        $this->assertEquals($expected, $templateOrigin);
    }

    public function testGetTemplateOriginWithShopExtensionTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $theme = 'theme-id';
        $extensionTemplate = new ShopExtensionTemplateType($name, $namespace, $theme);
        $expected = new ShopTemplateType($name);
        $templateOrigin = (new TemplateOriginResolver())->getTemplateOrigin($extensionTemplate);

        $this->assertInstanceOf(ShopTemplateType::class, $templateOrigin);
        $this->assertEquals($expected, $templateOrigin);
    }

    public function testGetTemplateOriginWithModuleTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $extensionTemplate = $expected = new ModuleTemplateType($name, $namespace);
        $templateOrigin = (new TemplateOriginResolver())->getTemplateOrigin($extensionTemplate);

        $this->assertInstanceOf(ModuleTemplateType::class, $templateOrigin);
        $this->assertEquals($expected, $templateOrigin);
    }

    public function testGetTemplateOriginWithModuleExtensionTemplate(): void
    {
        $name = 'template-name';
        $namespace = 'module-id';
        $parentNamespace = 'parent-module-id';
        $extensionTemplate = new ModuleExtensionTemplateType($name, $namespace, $parentNamespace);
        $expected = new ModuleTemplateType($name, $parentNamespace);
        $templateOrigin = (new TemplateOriginResolver())->getTemplateOrigin($extensionTemplate);

        $this->assertInstanceOf(ModuleTemplateType::class, $templateOrigin);
        $this->assertEquals($expected, $templateOrigin);
    }
}
