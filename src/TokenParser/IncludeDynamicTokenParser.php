<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Node\IncludeDynamicNode;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\NonTemplateFilenameException;
use Twig\Node\Expression\ConstantExpression;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

class IncludeDynamicTokenParser extends IncludeTokenParser
{
    public function __construct(
        private TemplateChainResolverInterface $templateChainResolver
    ) {
    }

    public function parse(Token $token): IncludeDynamicNode
    {
        $expression = $this->parser->getExpressionParser()->parseExpression();

        if ($expression instanceof ConstantExpression) {
            $this->replaceValue($expression);
        }

        [$variables, $only, $ignoreMissing] = $this->parseArguments();

        return new IncludeDynamicNode(
            $expression,
            $variables,
            $only,
            $ignoreMissing,
            $token->getLine(),
            $this->getTag()
        );
    }

    public function getTag(): string
    {
        return 'include_dynamic';
    }

    private function replaceValue(ConstantExpression $expression): void
    {
        $includeTagValue = $expression->getAttribute('value');
        $expression->setAttribute('value', $this->resolveTemplateNameToRender($includeTagValue));
    }

    private function resolveTemplateNameToRender(string $templateName): string
    {
        try {
            $renderedTemplate = $this->templateChainResolver->getLastChild($templateName);
        } catch (NonTemplateFilenameException) {
            $renderedTemplate = $templateName;
        }
        return $renderedTemplate;
    }
}
