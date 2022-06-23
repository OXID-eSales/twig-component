<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleTemplateExtensionChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainSorter;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainSorterInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateForModuleIdNotInChainException;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

final class TemplateChainSorterTest extends TestCase
{
    use ProphecyTrait;

    private TemplateChainSorterInterface $chainSorter;
    private ModuleTemplateExtensionChain|ObjectProphecy $moduleTemplateExtensionChain;
    private LoggerInterface|ObjectProphecy $logger;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSortWithNoConfig(): void
    {
        $templateModule1 = new ModuleTemplateType(
            'template1',
            'module1'
        );
        $templateModule2 = new ModuleTemplateType(
            'template1',
            'module2'
        );
        $templateName = 'template_with_different_name';
        $chain = new TemplateChain();
        $chain->append($templateModule1);
        $chain->append($templateModule2);
        $this->prepareChainSortersConfiguration($templateName, ['module2']);

        $sorted = $this->chainSorter->sort($chain, $templateModule2);

        $this->assertEquals($chain, $sorted);
    }

    public function testSortWithSingleEntry(): void
    {
        $templateModule1 = new ModuleTemplateType(
            'template1',
            'module1'
        );
        $templateModule2 = new ModuleTemplateType(
            'template1',
            'module2'
        );
        $templateName = '@module1/template1';
        $chain = new TemplateChain();
        $chain->append($templateModule1);
        $chain->append($templateModule2);
        $this->assertEquals($templateModule1, $chain->getLastChild());
        $this->prepareChainSortersConfiguration($templateName, ['module2']);

        $sorted = $this->chainSorter->sort($chain, $templateModule1);

        $this->assertEquals($templateModule2, $sorted->getLastChild());
    }

    public function testSortWithMultipleEntries(): void
    {
        $templateModule1 = new ModuleTemplateType(
            'template1',
            'module1'
        );
        $templateModule2 = new ModuleTemplateType(
            'template1',
            'module2'
        );
        $templateModule3 = new ModuleTemplateType(
            'template1',
            'module3'
        );
        $templateName = '@module1/template1';
        $chain = new TemplateChain();
        $chain->append($templateModule1);
        $chain->append($templateModule2);
        $chain->append($templateModule3);
        $this->assertEquals($templateModule1, $chain->getLastChild());
        $this->prepareChainSortersConfiguration($templateName, ['module3', 'module2', 'module1']);

        $chain = $this->chainSorter->sort($chain, $templateModule1);

        $this->assertEquals($templateModule3, $chain->getLastChild());
    }

    public function testSortWithUnknownModuleIdInConfigurationWillLogErrorWithModuleIdAndTemplateName(): void
    {
        $unknownModuleId = uniqid('moduleId_', true);
        $templateName = 'some/path/to/template1.html.twig';
        $templateModule1 = new ModuleTemplateType(
            $templateName,
            'module1'
        );
        $templateInSortingConfiguration = "@module1/$templateName";
        $chain = new TemplateChain();
        $chain->append($templateModule1);
        $this->prepareChainSortersConfiguration($templateInSortingConfiguration, [$unknownModuleId]);

        $chain = $this->chainSorter->sort($chain, $templateModule1);

        $this->logger
            ->error(
                Argument::allOf(
                    Argument::containingString($templateName),
                    Argument::containingString($unknownModuleId),
                )
            )
            ->shouldHaveBeenCalled();
    }

    public function testSortWithADuplicatedModuleIdInConfigurationWillCallLogger(): void
    {
        $moduleId = 'module1';
        $templateName = 'some/path/to/template1.html.twig';
        $templateModule1 = new ModuleTemplateType(
            $templateName,
            'module1'
        );
        $templateInSortingConfiguration = "@module1/$templateName";
        $chain = new TemplateChain();
        $chain->append($templateModule1);
        $this->prepareChainSortersConfiguration($templateInSortingConfiguration, [$moduleId, $moduleId]);

        $chain = $this->chainSorter->sort($chain, $templateModule1);

        $this->logger->error(Argument::type('string'))->shouldHaveBeenCalled();
    }

    private function prepareChainSortersConfiguration(string $templateName, array $loadOrder): void
    {
        $shopId = 1;
        $moduleTemplateExtensionsChain = new ModuleTemplateExtensionChain([$templateName => $loadOrder]);

        $shopConfiguration = $this->prophesize(ShopConfiguration::class);
        $shopConfigurationDao = $this->prophesize(ShopConfigurationDaoInterface::class);
        $context = $this->prophesize(ContextInterface::class);

        $shopConfiguration->getModuleTemplateExtensionChain()->willReturn($moduleTemplateExtensionsChain);
        $shopConfigurationDao->get($shopId)->willReturn($shopConfiguration);
        $context->getCurrentShopId()->willReturn($shopId);

        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->chainSorter = new TemplateChainSorter(
            $shopConfigurationDao->reveal(),
            $context->reveal(),
            $this->logger->reveal(),
        );
    }
}
