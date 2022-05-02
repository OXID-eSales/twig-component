<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\ChainProcessor\ChainProcessorInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeResolver;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;

class ModulesTemplateChainBuilder implements TemplateChainBuilderInterface
{
    public function __construct(
        private TemplateDirectoryResolverInterface $templateDirectoryResolver,
        private ChainProcessorInterface $modulesChainProcessor,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        $template = (new TemplateTypeResolver($templateName))->getTemplateType();
        foreach ($this->templateDirectoryResolver->getTemplateDirectories() as $directory) {
            $templateChain = $this->modulesChainProcessor->process($templateChain, $template, $directory);
        }

        return $templateChain;
    }
}
