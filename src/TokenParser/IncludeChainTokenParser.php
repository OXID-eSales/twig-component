<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

class IncludeChainTokenParser extends IncludeTokenParser
{
    /** @var TemplateChainResolverInterface */
    private $templateChainResolver;

    public function __construct(
        TemplateChainResolverInterface $templateChainResolver
    ) {
        $this->templateChainResolver = $templateChainResolver;
    }

    /**
     * @param Token $token
     * @return Node
     */
    public function parse(Token $token): Node
    {
        $expression = $this->parser->getExpressionParser()->parseExpression();

        if ($expression instanceof ConstantExpression) {
            $this->replaceValue($expression);
        }

        list($variables, $only, $ignoreMissing) = $this->parseArguments();
        return new IncludeNode($expression, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    private function replaceValue(ConstantExpression $expression): void
    {
        $includeTagValue = $expression->getAttribute('value');

        $templateToRender = $this->templateChainResolver->getLastChild($includeTagValue);
        $expression->setAttribute('value', $templateToRender);
    }
}
