<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\TemplateTypeResolver;
use Twig\Loader\FilesystemLoader;

use function array_merge;

class TemplateChainBuilderAggregate implements TemplateChainBuilderInterface
{
    public function __construct(
        private TemplateChainBuilderInterface $shopTemplateChainBuilder,
        private TemplateChainBuilderInterface $modulesTemplateChainBuilder,
        private TemplateChainValidatorInterface $templateChainValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getChain(string $templateName): array
    {
        $templateChains = [];
        $templateType = (new TemplateTypeResolver($templateName))->getTemplateType();
        $templateChains[] = $this->modulesTemplateChainBuilder->getChain($templateName);
        if ($this->isShopTemplate($templateType)) {
            $templateChains[] = $this->shopTemplateChainBuilder->getChain($templateName);
        }

        $templateChain = array_merge(... $templateChains);
        $this->templateChainValidator->validateTemplateChain($templateChain, $templateName);

        return $templateChain;
    }

    private function isShopTemplate(TemplateTypeInterface $templateType): bool
    {
        return $templateType->getParentNamespace() === FilesystemLoader::MAIN_NAMESPACE;
    }
}
