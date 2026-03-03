<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain\TemplateExtensionChainDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleTemplateExtensionChain;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\InvalidSortingConfigurationException;
use OxidEsales\Twig\Resolver\TemplateChain\SortingConfigurationValidatorInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainSorter;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainSorterInterface;
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
    private SortingConfigurationValidatorInterface|ObjectProphecy $sortingConfigurationValidator;
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
        $this->sortingConfigurationValidator
            ->validateModuleId($unknownModuleId, $chain, $templateModule1)
            ->willThrow(
                new InvalidSortingConfigurationException($unknownModuleId)
            );

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
        $this->sortingConfigurationValidator
            ->validateModuleId($moduleId, $chain, $templateModule1)
            ->willThrow(
                InvalidSortingConfigurationException::class
            );

        $chain = $this->chainSorter->sort($chain, $templateModule1);

        $this->logger->error(Argument::type('string'))->shouldHaveBeenCalled();
    }

    public function testSortWithMultipleCallsWillUseCachedShopConfiguration(): void
    {
        $shopId = 1;
        $templateModule1 = new ModuleTemplateType('template1', 'module1');
        $chain = new TemplateChain();
        $chain->append($templateModule1);

        $moduleTemplateExtensionsChain = new ModuleTemplateExtensionChain([]);

        $templateExtensionChainDao = $this->prophesize(TemplateExtensionChainDaoInterface::class);
        $templateExtensionChainDao->getChain($shopId)->willReturn($moduleTemplateExtensionsChain)->shouldBeCalledOnce();

        $context = $this->prophesize(ContextInterface::class);
        $context->getCurrentShopId()->willReturn($shopId);

        $this->sortingConfigurationValidator = $this->prophesize(SortingConfigurationValidatorInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->chainSorter = new TemplateChainSorter(
            $this->sortingConfigurationValidator->reveal(),
            $templateExtensionChainDao->reveal(),
            $context->reveal(),
            $this->logger->reveal(),
        );

        $chain1 = new TemplateChain();
        $chain1->append($templateModule1);
        $this->chainSorter->sort($chain1, $templateModule1);

        $chain2 = new TemplateChain();
        $chain2->append($templateModule1);
        $this->chainSorter->sort($chain2, $templateModule1);
    }

    private function prepareChainSortersConfiguration(string $templateName, array $loadOrder): void
    {
        $shopId = 1;
        $moduleTemplateExtensionsChain = new ModuleTemplateExtensionChain([$templateName => $loadOrder]);

        $templateExtensionChainDao = $this->prophesize(TemplateExtensionChainDaoInterface::class);
        $context = $this->prophesize(ContextInterface::class);

        $templateExtensionChainDao->getChain($shopId)->willReturn($moduleTemplateExtensionsChain);
        $context->getCurrentShopId()->willReturn($shopId);

        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->sortingConfigurationValidator = $this->prophesize(SortingConfigurationValidatorInterface::class);

        $this->chainSorter = new TemplateChainSorter(
            $this->sortingConfigurationValidator->reveal(),
            $templateExtensionChainDao->reveal(),
            $context->reveal(),
            $this->logger->reveal(),
        );
    }
}
