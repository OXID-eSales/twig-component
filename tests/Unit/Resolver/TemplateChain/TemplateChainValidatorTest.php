<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainInterface;
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

        $templateChainResolver = $this->prophesize(TemplateChainInterface::class);
        $templateChainResolver->getChain("$namespace/$templateName")->willReturn([
            'previous-template',
            'next-template'
        ]);
        $templateNameConverter = $this->prophesize(TemplateNameConverterInterface::class);
        $templateNameConverter->convertToFullyQualifiedTemplateName($templateName)->willReturn("$namespace/$templateName");

        $this->expectException(TemplateNotInChainException::class);

        (
        new TemplateChainValidator(
            $templateChainResolver->reveal(),
            $templateNameConverter->reveal()
        )
        )->isInChain($templateName);
    }

    /** @doesNotPerformAssertions */
    public function testIsInChainWithNameInChain(): void
    {
        $templateName = 'template.twig.html';
        $namespace = '@namespace';

        $templateChainResolver = $this->prophesize(TemplateChainInterface::class);
        $templateChainResolver->getChain("$namespace/$templateName")
            ->willReturn([
                'previous-template',
                "$namespace/$templateName",
                'next-template'
            ]);
        $templateNameConverter = $this->prophesize(TemplateNameConverterInterface::class);
        $templateNameConverter->convertToFullyQualifiedTemplateName($templateName)->willReturn("$namespace/$templateName");

        (
        new TemplateChainValidator(
            $templateChainResolver->reveal(),
            $templateNameConverter->reveal()
        )
        )->isInChain($templateName);
    }
}
