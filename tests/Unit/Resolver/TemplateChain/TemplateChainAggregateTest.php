<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainAggregate;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface;
use OxidEsales\Twig\Resolver\TemplateChain\UnresolvableTemplateNameException;
use OxidEsales\Twig\Resolver\TemplateNameConverterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class TemplateChainAggregateTest extends TestCase
{
    use ProphecyTrait;

    public function testGetChainWithTemplateThatCantBeResolvedWillThrowException(): void
    {
        $missingTemplateName = 'some-missing-template';
        $missingTemplateFullName = "@some-namespace/$missingTemplateName";
        $templateNameConverter = $this->prophesize(TemplateNameConverterInterface::class);
        $templateNameConverter
            ->convertToUnqualifiedTemplateName($missingTemplateFullName)
            ->willReturn($missingTemplateName);
        $templateChainResolver = $this->prophesize(TemplateChainInterface::class);
        $templateChainResolver
            ->getChain($missingTemplateName)
            ->willReturn([]);
        $templateChainAggregate = new TemplateChainAggregate(
            [
                $templateChainResolver->reveal(),
            ],
            $templateNameConverter->reveal()
        );

        $this->expectException(UnresolvableTemplateNameException::class);

        $templateChainAggregate->getChain($missingTemplateFullName);
    }
}
