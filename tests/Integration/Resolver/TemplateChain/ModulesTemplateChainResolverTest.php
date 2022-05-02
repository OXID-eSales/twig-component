<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\ModulesTemplateChainBuilder;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ModulesTemplateChainResolverTest extends TestCase
{
    use ContainerTrait;

    public function testGetChainWithNoActiveModules(): void
    {
        $this->installModule('moduleWithoutTwigExtension');
        $this->installModule('moduleWithTwigExtension');

        /** @var TemplateChainBuilderInterface $chainResolver */
        $chainResolver = $this->get(TemplateChainBuilderInterface::class);

        $this->assertEquals(
            [],
            $chainResolver->getChain('some-template.html.twig')
        );
    }

    public function testGetChainWithActiveModules(): void
    {
        $this->installModule('moduleWithoutTwigExtension');
        $this->installModule('moduleWithTwigExtension');

        /** @var ModuleActivationServiceInterface $moduleActivator */
        $moduleActivator = $this->get(ModuleActivationServiceInterface::class);
        $moduleActivator->activate('moduleWithoutTwigExtension', 1);
        $moduleActivator->activate('moduleWithTwigExtension', 1);

        /** @var TemplateChainBuilderInterface $chainResolver */
        $chainResolver = $this->get(TemplateChainBuilderInterface::class);

        $this->assertEquals(
            ['@moduleWithTwigExtension/some-template.html.twig'],
            $chainResolver->getChain('some-template.html.twig')
        );
    }

    public function testGetChainIfModuleOverwritesActiveTheme(): void
    {
        $this->installModule('moduleWithTwigExtensionForTheme');

        /** @var ModuleActivationServiceInterface $moduleActivator */
        $moduleActivator = $this->get(ModuleActivationServiceInterface::class);
        $moduleActivator->activate('moduleWithTwigExtensionForTheme', 1);

        $twigContext = $this->getMockBuilder(TwigContextInterface::class)->getMock();
        $twigContext->method('getActiveThemeId')->willReturn('customTheme');
        $filesystem = $this->createMock(\Symfony\Component\Filesystem\Filesystem::class);
        $filesystem->method('exists')->willReturn(true);

        $chainResolver = new ModulesTemplateChainBuilder(
            $this->get(ActiveModulesDataProviderInterface::class),
            $this->get(ModulesTemplateDirectoryResolverInterface::class),
            $twigContext,
            new Filesystem()
        );

        $this->assertEquals(
            ['@moduleWithTwigExtensionForTheme/customTheme/some-template.html.twig'],
            $chainResolver->getChain('some-template.html.twig')
        );
    }

    private function installModule(string $moduleId): void
    {
        /** @var ModuleInstallerInterface $moduleInstaller */
        $moduleInstaller = $this->get(ModuleInstallerInterface::class);

        $moduleInstaller->install(
            new OxidEshopPackage(
                __DIR__ . "/Fixtures/moduleTemplateChainResolverTest/$moduleId"
            )
        );
    }
}
