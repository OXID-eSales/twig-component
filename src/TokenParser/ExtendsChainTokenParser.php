<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Extensions\TemplateChainExtension;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class ExtendsChainTokenParser extends AbstractTokenParser
{
    private ?Token $token = null;

    /**
     * @param Token $token
     * @return Node
     * @throws SyntaxError
     * @see \Twig\TokenParser\ExtendsTokenParser::parse
     */
    public function parse(Token $token): Node
    {
        $this->token = $token;
        $this->validateTagUsage();
        $stream = $this->parser->getStream();
        $expression = $this->parser->getExpressionParser()->parseExpression();

        if ($expression instanceof ConstantExpression) {
            $fallback = $expression->getAttribute('value');
            $this->getTemplateChainExtension()->assertValidTemplateName($fallback);
            $expression = $this->createRuntimeResolutionExpression(
                $this->getTemplateName(),
                $fallback,
                $token->getLine()
            );
        }

        $this->parser->setParent($expression);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new Node();
    }

    public function getTag(): string
    {
        return 'extends';
    }

    private function getTemplateName(): string
    {
        return $this->parser->getStream()->getSourceContext()->getName();
    }

    /**
     * @throws SyntaxError
     * @see same validations as in \Twig\TokenParser\ExtendsTokenParser::parse
     */
    private function validateTagUsage(): void
    {
        $stream = $this->parser->getStream();

        if ($this->parser->peekBlockStack()) {
            throw new SyntaxError(
                'Cannot use "extend" in a block.',
                $this->token->getLine(),
                $stream->getSourceContext()
            );
        }
        if (!$this->parser->isMainScope()) {
            throw new SyntaxError(
                'Cannot use "extend" in a macro.',
                $this->token->getLine(),
                $stream->getSourceContext()
            );
        }
        if ($this->parser->getParent() !== null) {
            throw new SyntaxError(
                'Multiple extends tags are forbidden.',
                $this->token->getLine(),
                $stream->getSourceContext()
            );
        }
    }

    private function getTemplateChainExtension(): TemplateChainExtension
    {
        return $this->parser->getEnvironment()->getExtension(TemplateChainExtension::class);
    }

    private function createRuntimeResolutionExpression(string $currentTemplateName, string $fallback, int $line): FunctionExpression
    {
        $twigFunction = $this->parser->getEnvironment()->getFunction('oxid_resolve_parent');
        return new FunctionExpression(
            $twigFunction,
            new Node([
                new ConstantExpression($currentTemplateName, $line),
                new ConstantExpression($fallback, $line),
            ]),
            $line
        );
    }
}
