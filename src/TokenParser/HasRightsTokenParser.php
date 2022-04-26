<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Node\HasRightsNode;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class HasRightsTokenParser extends AbstractTokenParser
{
    public function __construct(private string $nodeClass)
    {
    }

    /**
     * @param Token $token
     *
     * @return HasRightsNode
     *
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $variables = $this->getVariables();

        $continue = true;
        while ($continue) {
            // create subtree until the decideMyTagFork() callback returns true
            $body = $this->parser->subparse([$this, 'decideMyTagFork']);

            if ($stream->next()->getValue() === 'endhasrights') {
                $continue = false;
            } else {
                throw new SyntaxError(sprintf('Unexpected end of template. Twig was looking for the following tags "endhasrights" to close the "hasrights" block started at line %d)', $lineno), -1);
            }

            $stream->expect(Token::BLOCK_END_TYPE);
        }

        return new $this->nodeClass($body, $variables, $lineno, $this->getTag());
    }

    /**
     * @return Node
     */
    private function getVariables(): Node
    {
        $stream = $this->parser->getStream();
        $variables = $this->parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);

        return $variables;
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function decideMyTagFork(Token $token): bool
    {
        return $token->test(['endhasrights']);
    }

    /**
     * Your tag name: if the parsed tag match the one you put here, your parse()
     * method will be called.
     *
     * @return string
     */
    public function getTag(): string
    {
        return 'hasrights';
    }
}
