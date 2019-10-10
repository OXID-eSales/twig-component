<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Node\CaptureNode;
use Twig\Error\SyntaxError;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\TokenStream;

/**
 * Class CaptureTokenParser
 *
 * @package OxidEsales\Twig\TokenParser
 */
class CaptureTokenParser extends AbstractTokenParser
{
    private $possibleAttributes = ['name', 'assign', 'append'];

    /**
     * @param Token $token
     *
     * @return CaptureNode|\Twig_Node
     * @throws SyntaxError
     */
    public function parse(Token $token): CaptureNode
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $attributeName = $this->getAttributeName($stream);
        $stream->expect(Token::OPERATOR_TYPE, '=');
        $variableName = $parser->getExpressionParser()->parseExpression()->getAttribute('value');
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new CaptureNode($attributeName, $variableName, $body, $token->getLine(), $this->getTag());
    }

    /**
     * @param TokenStream $stream
     *
     * @return string
     * @throws SyntaxError
     */
    private function getAttributeName(TokenStream $stream): string
    {
        $attributeName = $stream->expect(Token::NAME_TYPE)->getValue();
        if (!in_array($attributeName, $this->possibleAttributes)) {
            throw new SyntaxError("Incorrect attribute name. Possible attribute names are: 'name', 'assign' and 'append'");
        }

        return $attributeName;
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function decideBlockEnd(Token $token): bool
    {
        return $token->test(['endcapture']);
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'capture';
    }
}
