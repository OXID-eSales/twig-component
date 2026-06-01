<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\NonTemplateFilenameException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateChainExtension extends AbstractExtension
{
    public function __construct(
        private TemplateChainResolverInterface $templateChainResolver
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('oxid_resolve_last_child', [$this, 'resolveLastChild']),
            new TwigFunction('oxid_resolve_parent', [$this, 'resolveParent']),
        ];
    }

    public function assertValidTemplateName(string $templateName): void
    {
        $this->templateChainResolver->getLastChild($templateName);
    }

    public function resolveLastChild(string $templateName): string
    {
        try {
            return $this->templateChainResolver->getLastChild($templateName);
        } catch (NonTemplateFilenameException) {
            return $templateName;
        }
    }

    public function resolveParent(string $currentTemplateName, string $fallback): string
    {
        if (!$this->templateChainResolver->hasParent($currentTemplateName)) {
            return $fallback;
        }
        return $this->templateChainResolver->getParent($currentTemplateName);
    }
}
