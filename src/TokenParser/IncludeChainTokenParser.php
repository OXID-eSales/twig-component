<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Token;

class IncludeChainTokenParser extends AbstractIncludeChainTokenParser
{
    public function parse(Token $token): Node
    {
        $expression = $this->parser->getExpressionParser()->parseExpression();

        if ($expression instanceof ConstantExpression) {
            $expression = $this->createRuntimeResolutionExpression(
                $expression->getAttribute('value'),
                $token->getLine()
            );
        }

        [$variables, $only, $ignoreMissing] = $this->parseArguments();
        return new IncludeNode($expression, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }
}
