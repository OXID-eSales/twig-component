<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\TokenParser;

use OxidEsales\Twig\TokenParser\CaptureTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\LoaderInterface;
use Twig\Parser;
use Twig\Source;
use Twig\Token;

final class CaptureTokenParserTest extends TestCase
{
    private Environment $environment;
    private Parser $parser;
    private CaptureTokenParser $captureTokenParser;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $this->environment = new Environment($loader, ['cache' => false]);

        $this->captureTokenParser = new CaptureTokenParser();
        $this->environment->addTokenParser($this->captureTokenParser);

        $this->parser = new Parser($this->environment);
    }

    /** @covers CaptureTokenParser::getTag */
    public function testGetTag(): void
    {
        $this->assertEquals('capture', $this->captureTokenParser->getTag());
    }

    /** @covers CaptureTokenParser::decideBlockEnd */
    public function testDecideBlockEnd(): void
    {
        $token = new Token(Token::NAME_TYPE, 'foo', 1);
        $this->assertEquals(false, $this->captureTokenParser->decideBlockEnd($token));

        $token = new Token(Token::NAME_TYPE, 'endcapture', 1);
        $this->assertEquals(true, $this->captureTokenParser->decideBlockEnd($token));
    }

    /**
     * @param $source
     *
     * @covers       CaptureTokenParser::parse
     * @dataProvider templateSourceCodeProvider
     */
    public function testParse($source): void
    {
        $stream = $this->environment->tokenize(new Source($source, 'index'));
        $node = $this->parser->parse($stream);

        $this->assertTrue($node->hasNode('body'));
        $bodyNode = $node->getNode('body');

        $captureNode = $bodyNode->getNode('0');
        $this->assertTrue($captureNode->hasAttribute('attributeName'));
        $this->assertTrue($captureNode->hasAttribute('variableName'));

        $ifContentNode = $bodyNode->getIterator()[0];

        $this->assertTrue($ifContentNode->hasNode('body'));
    }

    public function templateSourceCodeProvider(): array
    {
        return [
            ["{% capture name = \"foo\" %}Lorem Ipsum{% endcapture %}"],
            ["{% capture assign = \"foo\" %}Lorem Ipsum{% endcapture %}"],
            ["{% capture append = \"foo\" %}Lorem Ipsum{% endcapture %}"],
        ];
    }

    /** @covers CaptureTokenParser::parse */
    public function testTwigErrorSyntaxIsThrown(): void
    {
        $source = '{% capture %}foo{% /endcapture %}';
        $stream = $this->environment->tokenize(new Source($source, 'index'));

        $this->expectException(SyntaxError::class);
        $this->parser->parse($stream);
    }

    /** @covers CaptureTokenParser::parse */
    public function testParseException(): void
    {
        $source = "{% capture foo = \"foo\" %}Lorem Ipsum{% endcapture %}";
        $stream = $this->environment->tokenize(new Source($source, 'index'));

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage("Incorrect attribute name. Possible attribute names are: 'name', 'assign' and 'append'");
        $this->parser->parse($stream);
    }
}
