<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolver;
use PHPUnit\Framework\TestCase;

final class ModulesTemplateDirectoryResolverTest extends TestCase
{
    public function testGetAbsolutePath(): void
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context->method('getDefaultShopId')->willReturn(1);

        $modulePathResolver = $this->getMockBuilder(ModulePathResolverInterface::class)->getMock();
        $modulePathResolver->method('getFullModulePathFromConfiguration')->willReturn('module-path');

        $this->assertEquals(
            'module-path/views/twig/tpl',
            (new ModulesTemplateDirectoryResolver($modulePathResolver, $context))->getAbsolutePath('someModule')
        );
    }
}
