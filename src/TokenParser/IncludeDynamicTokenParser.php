<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Node\IncludeDynamicNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Token;

class IncludeDynamicTokenParser extends AbstractIncludeChainTokenParser
{
    public function parse(Token $token): IncludeDynamicNode
    {
        $expression = $this->parser->getExpressionParser()->parseExpression();

        if ($expression instanceof ConstantExpression) {
            $expression = $this->createRuntimeResolutionExpression(
                $expression->getAttribute('value'),
                $token->getLine()
            );
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
}
