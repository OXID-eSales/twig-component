<?php

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainValidatorInterface;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenParser\ExtendsTokenParser;

class ExtendsChainTokenParser extends AbstractTokenParser
{
    /** @var TemplateChainResolverInterface */
    private $templateChainResolver;
    /** @var Token */
    private $token;
    /** @var TemplateChainValidatorInterface */
    private $templateChainValidator;

    public function __construct(
        TemplateChainResolverInterface $templateChainResolver,
        TemplateChainValidatorInterface $templateChainValidator
    ) {
        $this->templateChainResolver = $templateChainResolver;
        $this->templateChainValidator = $templateChainValidator;
    }

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

        if (
            $expression instanceof ConstantExpression
            && $this->templateChainResolver->hasParent($this->getTemplateName())
        ) {
            $this->replaceValue($expression);
        }

        $this->parser->setParent($expression);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new Node();
    }

    public function getTag(): string
    {
        return 'extends';
    }

    private function replaceValue(ConstantExpression $expression): void
    {
        $extendsTagValue = $expression->getAttribute('value');
        $this->templateChainValidator->isInChain($extendsTagValue);

        $templateName = $this->parser->getStream()->getSourceContext()->getName();
        $parentInHierarchy = $this->templateChainResolver->getParent($templateName);
        $expression->setAttribute('value', $parentInHierarchy);
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
}
