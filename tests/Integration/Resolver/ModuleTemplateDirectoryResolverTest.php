<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Twig\Resolver\ModuleTemplateChainResolverInterface;
use PHPUnit\Framework\TestCase;

final class ModuleTemplateDirectoryResolverTest extends TestCase
{
    use ContainerTrait;

    public function testGetChainWithNoActiveModules(): void
    {
        $this->installTestModules();

        /** @var ModuleTemplateChainResolverInterface $chainResolver */
        $chainResolver = $this->get(ModuleTemplateChainResolverInterface::class);

        $this->assertEquals(
            [],
            $chainResolver->getChain('some-template.html.twig')
        );
    }

    public function testGetChainWithActiveModules(): void
    {
        $this->installTestModules();

        /** @var ModuleActivationServiceInterface $moduleActivator */
        $moduleActivator = $this->get(ModuleActivationServiceInterface::class);
        $moduleActivator->activate('moduleWithoutTwigExtension', 1);
        $moduleActivator->activate('moduleWithTwigExtension', 1);

        /** @var ModuleTemplateChainResolverInterface $chainResolver */
        $chainResolver = $this->get(ModuleTemplateChainResolverInterface::class);

        $this->assertEquals(
            ['@moduleWithTwigExtension/some-template.html.twig'],
            $chainResolver->getChain('some-template.html.twig')
        );
    }

    private function installTestModules(): void
    {
        /** @var ModuleInstallerInterface $moduleInstaller */
        $moduleInstaller = $this->get(ModuleInstallerInterface::class);

        $moduleInstaller->install(new OxidEshopPackage(
                'moduleWithoutTwigExtension',
                __DIR__ . '/Fixtures/moduleWithoutTwigExtension')
        );

        $moduleInstaller->install(new OxidEshopPackage(
                'moduleWithTwigExtension',
                __DIR__ . '/Fixtures/moduleWithTwigExtension')
        );
    }
}
