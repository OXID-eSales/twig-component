<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\DependencyInjection\Compiler;

use OxidEsales\Twig\TwigEngine;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TwigEscaperPass
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class TwigEscaperPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('twig')) {
            return;
        }

        $escapers = $container->findTaggedServiceIds('twig.escaper', true);
        $twigEngine = $container->getDefinition(TwigEngine::class);

        foreach ($escapers as $id => $attributes) {
            $twigEngine->addMethodCall('addEscaper', [new Reference($id)]);
        }

        return;
    }
}
