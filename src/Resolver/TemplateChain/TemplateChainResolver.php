<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeFactoryInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TemplateChainResolver implements TemplateChainResolverInterface
{
    public function __construct(
        private TemplateChainBuilderInterface $templateChainBuilder,
        private TemplateTypeFactoryInterface $templateTypeFactory,
        private TagAwareCacheInterface $cache,
    ) {
    }

    public function getParent(string $templateName): string
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);

        return $this->getTemplateChain($templateName)->getParent($templateType)->getFullyQualifiedName();
    }

    public function getLastChild(string $templateName): string
    {
        return $this->getTemplateChain($templateName)->getLastChild()->getFullyQualifiedName();
    }

    public function hasParent(string $templateName): bool
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);

        return $this->getTemplateChain($templateName)->hasParent($templateType);
    }

    private function getTemplateChain(string $templateName): TemplateChain
    {
        return $this->cache->get(
            $this->getCacheKey($templateName),
            function (ItemInterface $item) use ($templateName): TemplateChain {
                $item->tag('oxid_esales.cache.twig.template_chain');

                return $this->createTemplateChain($templateName);
            }
        );
    }

    private function createTemplateChain(string $templateName): TemplateChain
    {
        $templateType = $this->templateTypeFactory->createFromTemplateName($templateName);

        return $this->templateChainBuilder->getChain($templateType);
    }

    private function getCacheKey(string $templateName): string
    {
        return 'twig_template_chain_' . sha1($templateName);
    }
}
