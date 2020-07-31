<?php

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Resolver\TemplateHierarchyResolverInterface;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenParser\ExtendsTokenParser;

class ExtendsHierarchyTokenParser extends AbstractTokenParser
{
    /** @var TemplateHierarchyResolverInterface */
    private $templateHierarchyResolver;
    /** @var Token */
    private $token;

    public function __construct(
        TemplateHierarchyResolverInterface $templateHierarchyResolver
    ) {
        $this->templateHierarchyResolver = $templateHierarchyResolver;
    }

    /**
     * @see \Twig\TokenParser\ExtendsTokenParser::parse
     * @param Token $token
     * @return Node
     * @throws SyntaxError
     */
    public function parse(Token $token): Node
    {
        $this->token = $token;
        $this->validateTagUsage();
        $stream = $this->parser->getStream();
        $extendsExpression = $this->parser->getExpressionParser()->parseExpression();

        $parentInHierarchy = $this->templateHierarchyResolver->getParentForTemplate(
            $stream->getSourceContext()->getName(),
            $extendsExpression->getAttribute('value')
        );
        $extendsExpression->setAttribute('value', $parentInHierarchy);

        $this->parser->setParent($extendsExpression);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new Node();
    }

    public function getTag(): string
    {
        return 'extends';
    }

    /**
     * @see same validations as in \Twig\TokenParser\ExtendsTokenParser::parse
     * @throws SyntaxError
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
}
