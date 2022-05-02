<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainBuilderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainValidator;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateNotInChainException;
use OxidEsales\Twig\Resolver\TemplateNameConverterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class TemplateChainValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testIsInChainWithNameNotInChain(): void
    {
        $templateName = 'template.twig.html';
        $namespace = '@namespace';

        $templateChainResolver = $this->prophesize(TemplateChainBuilderInterface::class);
        $chain = [
            'previous-template',
            'next-template'
        ];
        $templateChainResolver->getChain("$namespace/$templateName")->willReturn($chain);
        $this->expectException(TemplateNotInChainException::class);

        (new TemplateChainValidator())->validateTemplateChain(
            $chain,
            $templateName
        );
    }

    /** @doesNotPerformAssertions */
    public function testIsInChainWithNameInChain(): void
    {
        $templateName = 'template.twig.html';
        $namespace = '@namespace';

        $templateChainResolver = $this->prophesize(TemplateChainBuilderInterface::class);
        $chain = [
            'previous-template',
            "$namespace/$templateName",
            'next-template'
        ];
        $templateChainResolver->getChain("$namespace/$templateName")
            ->willReturn($chain);
        (new TemplateChainValidator())
            ->validateTemplateChain($chain, "$namespace/$templateName");
    }
}
