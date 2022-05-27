<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ChainAppenderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;

class TemplateChainBuilder implements TemplateChainBuilderInterface
{
    public function __construct(
        private TemplateDirectoryResolverInterface $templateDirectoryResolver,
        /** @var ChainAppenderInterface[] */
        private array $chainAppenders,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getChain(TemplateTypeInterface $templateType): TemplateChain
    {
        $templateChain = new TemplateChain();
        foreach ($this->templateDirectoryResolver->getTemplateDirectories() as $directory) {
            /** @var ChainAppenderInterface $chainAppender */
            foreach ($this->chainAppenders as $chainAppender) {
                $templateChain = $chainAppender->addToChain($templateChain, $templateType, $directory);
            }
        }
        return $templateChain;
    }
}
