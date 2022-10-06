<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

class IncludeContentTokenParser extends IncludeTokenParser
{
    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        // Replace $expr with wrapper equal to {% include template_from_string(content(value)) %}
        $contentNameNode = new Node([new ConstantExpression($expr->getAttribute('value'), $token->getLine())]);
        $contentNode = new Node([new FunctionExpression('content', $contentNameNode, $token->getLine())]);
        $expr = new FunctionExpression('template_from_string', $contentNode, $token->getLine());

        return new IncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'include_content';
    }
}
