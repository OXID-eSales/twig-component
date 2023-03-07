<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeDynamicLogic;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\TokenParser\IncludeChainTokenParser;
use OxidEsales\Twig\TokenParser\IncludeDynamicTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

class IncludeExtension extends AbstractExtension
{
    public function __construct(
        private IncludeDynamicLogic $includeDynamicLogic,
        private TemplateChainResolverInterface $templateChainResolver
    ) {
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [
            new IncludeChainTokenParser(
                $this->templateChainResolver,
            ),
            new IncludeDynamicTokenParser(
                $this->templateChainResolver
            ),
        ];
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderForCache(array $parameters): string
    {
        return $this->includeDynamicLogic->renderForCache($parameters);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function includeDynamicPrefix(array $parameters): array
    {
        return $this->includeDynamicLogic->includeDynamicPrefix($parameters);
    }
}
