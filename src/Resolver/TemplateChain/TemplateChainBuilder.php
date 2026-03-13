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
    private array $cachedDirectories = [];

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
        foreach ($this->getDirectories() as $directory) {
            /** @var ChainAppenderInterface $chainAppender */
            foreach ($this->chainAppenders as $chainAppender) {
                $templateChain = $chainAppender->addToChain($templateChain, $templateType, $directory);
            }
        }
        return $templateChain;
    }

    private function getDirectories(): array
    {
        if (!$this->cachedDirectories) {
            $this->cachedDirectories = $this->templateDirectoryResolver->getTemplateDirectories();
        }

        return $this->cachedDirectories;
    }
}
