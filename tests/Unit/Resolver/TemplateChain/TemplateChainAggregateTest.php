<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderAggregate;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainValidatorInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;
use OxidEsales\Twig\Resolver\TemplateChain\UnresolvableTemplateNameException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class TemplateChainAggregateTest extends TestCase
{
    use ProphecyTrait;
    use ContainerTrait;

    public function testGetChainWithTemplateThatCantBeResolvedWillThrowException(): void
    {
        $missingTemplateName = 'some-missing-template';
        $missingTemplateFullName = "@some-namespace/$missingTemplateName";
        $templateChainValidator = $this->prophesize(TemplateChainValidatorInterface::class);
        $templateChainBuilder = $this->prophesize(TemplateChainBuilderInterface::class);
        $templateChainBuilder
            ->getChain($missingTemplateFullName)
            ->willReturn([]);
        $templateChainAggregate = new TemplateChainBuilderAggregate(
            [
                $templateChainBuilder->reveal(),
            ],
            $this->get(TemplateChainValidatorInterface::class)
        );

        $this->expectException(TemplateNotInChainException::class);

        $templateChainAggregate->getChain($missingTemplateFullName);
    }
}
