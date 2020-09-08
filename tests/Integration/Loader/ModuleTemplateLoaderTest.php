<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Twig\Loader\ModuleTemplateLoader;
use OxidEsales\Twig\Resolver\ModuleTemplateDirectoryResolverInterface;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class ModuleTemplateLoaderTest extends TestCase
{
    use ContainerTrait;

    protected function tearDown(): void
    {
        $this->unInstallTestModule();
        parent::tearDown();
    }

    public function testModuleTemplateLoading(): void
    {
        $this->installTestModule();
        $this->activateTestModule();

        $moduleTemplateName = '@moduleWithTwigExtension/some-template.html.twig';

        $this->assertEquals(
            $this->getModuleTemplateAbsolutePath(),
            $this->getLoader()->findTemplate($moduleTemplateName)
        );
    }

    public function testFindTemplateWithInactiveModule(): void
    {
        $this->installTestModule();
        $this->activateTestModule();
        $this->deactivateTestModule();

        $moduleTemplateName = '@moduleWithTwigExtension/some-template.html.twig';

        $this->assertNull(
            $this->getLoader()->findTemplate($moduleTemplateName)
        );
    }

    public function testLoadModuleTemplateWithShopTemplateNameIfModuleParentTemplateExists(): void
    {
        $this->installTestModule();
        $this->activateTestModule();

        $shopTemplateName = 'some-template.html.twig';

        $this->assertEquals(
            $this->getModuleTemplateAbsolutePath(),
            $this->getLoader()->findTemplate($shopTemplateName)
        );
    }

    public function testDoesNotLoadAnythingWithShopTemplateNameAndNoModuleParentTemplate(): void
    {
        $shopTemplateName = 'some-template.html.twig';

        $this->assertNull(
            $this->getLoader()->findTemplate($shopTemplateName)
        );
    }

    public function testLoadsModuleTemplateWithBCShopTemplateNameAndModuleParentTemplate(): void
    {
        $this->installTestModule();
        $this->activateTestModule();

        $BCShopTemplateName = 'some-template.tpl';

        $this->assertEquals(
            $this->getModuleTemplateAbsolutePath(),
            $this->getLoader()->findTemplate($BCShopTemplateName)
        );
    }

    private function installTestModule(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage());
    }

    private function unInstallTestModule(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage());
    }
    private function activateTestModule(): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->activate('moduleWithTwigExtension', 1);
    }

    private function deactivateTestModule(): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate('moduleWithTwigExtension', 1);
    }

    private function getLoader(): ModuleTemplateLoader
    {
        return $this->get(ModuleTemplateLoader::class);
    }

    private function getModuleTemplateAbsolutePath(): string
    {
        return Path::join(
            $this->get(ModuleTemplateDirectoryResolverInterface::class)->getAbsolutePath('moduleWithTwigExtension'),
            'some-template.html.twig'
        );
    }

    private function getTestPackage(): OxidEshopPackage
    {
        $packageFixturePath = __DIR__ . '/Fixtures/moduleWithTwigExtension';
        return new OxidEshopPackage('moduleWithTwigExtension', $packageFixturePath);
    }
}
