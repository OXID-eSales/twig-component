<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use OxidEsales\Twig\Extensions\MathExtension;
use OxidEsales\Twig\DependencyInjection\Compiler\TwigEnvironmentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TwigEnvironmentPassTest extends TestCase
{
    public function testTwigBridgeExtensionsAreRegisteredFirst(): void
    {
        $container = new ContainerBuilder();
        $twigDefinition = $container->register('twig');
        $container->register('other_extension', 'Foo\Bar')
            ->addTag('twig.extension');
        $container->register('twig_bridge_extension', MathExtension::class)
            ->addTag('twig.extension');

        $twigEnvironmentPass = new TwigEnvironmentPass();
        $twigEnvironmentPass->process($container);

        $methodCalls = $twigDefinition->getMethodCalls();
        $this->assertCount(2, $methodCalls);

        $otherExtensionReference = $methodCalls[0][1][0];
        $this->assertInstanceOf(Reference::class, $otherExtensionReference);
        $this->assertSame('other_extension', (string) $otherExtensionReference);

        $twigBridgeExtensionReference = $methodCalls[1][1][0];
        $this->assertInstanceOf(Reference::class, $twigBridgeExtensionReference);
        $this->assertSame('twig_bridge_extension', (string) $twigBridgeExtensionReference);
    }
}
