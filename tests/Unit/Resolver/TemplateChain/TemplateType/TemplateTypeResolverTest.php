<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain\TemplateType;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolver;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeFactory;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopExtensionTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use PHPUnit\Framework\TestCase;

final class TemplateTypeResolverTest extends TestCase
{
    private string $extension = 'html.twig';

    public function testGetTemplateTypeWithShopTemplate(): void
    {
        $templatePath = "start/hello.$this->extension";
        $template = $this->getTemplateType($templatePath);

        $this->assertInstanceOf(ShopTemplateType::class, $template);
        $this->assertEquals($templatePath, $template->getName());
        $this->assertEquals('__main__', $template->getNamespace());
        $this->assertEquals('__main__', $template->getParentNamespace());
        $this->assertEquals($templatePath, $template->getRelativeFilePath());
        $this->assertEquals("@__main__/$templatePath", $template->getFullyQualifiedName());
    }

    public function testGetTemplateTypeWithShopTemplateAndMainNamespace(): void
    {
        $baseTemplateName = "start/hello.$this->extension";
        $templatePath = "@__main__/$baseTemplateName";
        $template = $this->getTemplateType($templatePath);

        $this->assertInstanceOf(ShopTemplateType::class, $template);
        $this->assertEquals($baseTemplateName, $template->getName());
        $this->assertEquals('__main__', $template->getNamespace());
        $this->assertEquals('__main__', $template->getParentNamespace());
        $this->assertEquals($baseTemplateName, $template->getRelativeFilePath());
        $this->assertEquals($templatePath, $template->getFullyQualifiedName());
    }

    public function testGetTemplateTypeWithModuleTemplate(): void
    {
        $moduleId = 'moduleId';
        $baseTemplateName = "start/hello.$this->extension";
        $templatePath = "@$moduleId/$baseTemplateName";
        $template = $this->getTemplateType($templatePath);

        $this->assertInstanceOf(ModuleTemplateType::class, $template);
        $this->assertEquals($baseTemplateName, $template->getName());
        $this->assertEquals($moduleId, $template->getNamespace());
        $this->assertEquals($moduleId, $template->getParentNamespace());
        $this->assertEquals($baseTemplateName, $template->getRelativeFilePath());
        $this->assertEquals($templatePath, $template->getFullyQualifiedName());

    }

    public function testGetTemplateTypeWithShopExtension(): void
    {
        $moduleId = 'moduleId';
        $themeId = 'some-theme-id';
        $baseTemplateName = "start/hello.$this->extension";
        $relativePath = "extensions/themes/$themeId/$baseTemplateName";
        $templatePath = "@$moduleId/$relativePath";
        $template = $this->getTemplateType($templatePath);

        $this->assertInstanceOf(ShopExtensionTemplateType::class, $template);
        $this->assertEquals($baseTemplateName, $template->getName());
        $this->assertEquals($moduleId, $template->getNamespace());
        $this->assertEquals('__main__', $template->getParentNamespace());
        $this->assertEquals($relativePath, $template->getRelativeFilePath());
        $this->assertEquals($templatePath, $template->getFullyQualifiedName());
    }

    public function testGetTemplateTypeWithModuleExtension(): void
    {
        $moduleId = 'moduleId';
        $extendsModuleId = 'some-module-id';
        $baseTemplateName = "start/hello.$this->extension";
        $relativePath = "extensions/modules/$extendsModuleId/$baseTemplateName";
        $templatePath = "@$moduleId/$relativePath";
        $template = $this->getTemplateType($templatePath);

        $this->assertInstanceOf(ModuleExtensionTemplateType::class, $template);
        $this->assertEquals($baseTemplateName, $template->getName());
        $this->assertEquals($moduleId, $template->getNamespace());
        $this->assertEquals($extendsModuleId, $template->getParentNamespace());
        $this->assertEquals($relativePath, $template->getRelativeFilePath());
        $this->assertEquals($templatePath, $template->getFullyQualifiedName());
    }

    private function getTemplateType(string $templatePath): TemplateTypeInterface
    {
        return (new TemplateTypeFactory((new TemplateFileResolver($this->extension))))->createFromTemplateName($templatePath);
    }
}
