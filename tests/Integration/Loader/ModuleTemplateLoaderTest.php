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
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use OxidEsales\Twig\Loader\ModuleTemplateLoader;
use OxidEsales\Twig\Resolver\ModuleTemplateDirectoryResolverInterface;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class ModuleTemplateLoaderTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    public function setUp()
    {
        $this->databaseRestorer = new DatabaseRestorer();
        $this->databaseRestorer->dumpDB(__CLASS__);
    }

    protected function tearDown()
    {
        $this->databaseRestorer->restoreDB(__CLASS__);
    }

    public function testModuleTemplateLoading(): void
    {
        $this->installModuleAndActivateTestModule();

        $moduleTemplateName = '@moduleWithTwigExtension/some-template.html.twig';

        $this->assertEquals(
            $this->getModuleTemplateAbsolutePath(),
            $this->getLoader()->findTemplate($moduleTemplateName)
        );
    }

    public function testLoadModuleTemplateWithShopTemplateNameIfModuleParentTemplateExists(): void
    {
        $this->installModuleAndActivateTestModule();

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
        $this->installModuleAndActivateTestModule();

        $BCShopTemplateName = 'some-template.tpl';

        $this->assertEquals(
            $this->getModuleTemplateAbsolutePath(),
            $this->getLoader()->findTemplate($BCShopTemplateName)
        );
    }

    private function installModuleAndActivateTestModule(): void
    {
        /** @var ModuleInstallerInterface $moduleInstaller */
        $moduleInstaller = $this->get(ModuleInstallerInterface::class);

        $moduleInstaller->install(new OxidEshopPackage(
            'moduleWithTwigExtension',
            __DIR__ . '/Fixtures/moduleWithTwigExtension'
        ));

        $this
            ->get(ModuleActivationServiceInterface::class)
            ->activate('moduleWithTwigExtension', 1);
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
}
