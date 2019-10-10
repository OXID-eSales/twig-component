<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Node\IncludeDynamicNode;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

/**
 * Class IncludeDynamicTokenParser
 */
class IncludeDynamicTokenParser extends IncludeTokenParser
{
    /**
     * @param \Twig_Token $token
     *
     * @return IncludeDynamicNode|\Twig_Node|\Twig_Node_Include
     */
    public function parse(Token $token): IncludeDynamicNode
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        return new IncludeDynamicNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'include_dynamic';
    }
}
