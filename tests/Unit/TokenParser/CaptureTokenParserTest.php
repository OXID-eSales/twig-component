<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\TokenParser;

use OxidEsales\Twig\TokenParser\CaptureTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Parser;
use Twig\Source;
use Twig\Token;

class CaptureTokenParserTest extends TestCase
{

    /** @var Environment */
    private $environment;

    /** @var Parser */
    private $parser;

    /** @var CaptureTokenParser */
    private $captureTokenParser;

    /**
     * Set up
     */
    protected function setUp()
    {
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $this->environment = new \Twig_Environment($loader, ['cache' => false]);

        $this->captureTokenParser = new CaptureTokenParser();
        $this->environment->addTokenParser($this->captureTokenParser);

        $this->parser = new Parser($this->environment);
    }

    /**
     * @covers CaptureTokenParser::getTag
     */
    public function testGetTag()
    {
        $this->assertEquals('capture', $this->captureTokenParser->getTag());
    }

    /**
     * @covers CaptureTokenParser::decideBlockEnd
     */
    public function testDecideBlockEnd()
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
    public function testParse($source)
    {
        $stream = $this->environment->tokenize(new Source($source, 'index'));
        $node = $this->parser->parse($stream);

        $this->assertTrue($node->hasNode('body'));
        $bodyNode = $node->getNode('body');

        $captureNode = $bodyNode->getNode(0);
        $this->assertTrue($captureNode->hasAttribute('attributeName'));
        $this->assertTrue($captureNode->hasAttribute('variableName'));

        $ifContentNode = $bodyNode->getIterator()[0];

        $this->assertTrue($ifContentNode->hasNode('body'));
    }

    /**
     * @return array
     */
    public function templateSourceCodeProvider()
    {
        return [
            ["{% capture name = \"foo\" %}Lorem Ipsum{% endcapture %}"],
            ["{% capture assign = \"foo\" %}Lorem Ipsum{% endcapture %}"],
            ["{% capture append = \"foo\" %}Lorem Ipsum{% endcapture %}"],
        ];
    }

    /**
     * @covers       CaptureTokenParser::parse
     * @expectedException \Twig_Error_Syntax
     */
    public function testTwigErrorSyntaxIsThrown()
    {
        $source = '{% capture %}foo{% /endcapture %}';

        $stream = $this->environment->tokenize(new Source($source, 'index'));
        $this->parser->parse($stream);
    }

    /**
     * @covers CaptureTokenParser::parse
     * @expectedException \Twig_Error_Syntax
     */
    public function testParseException()
    {
        $source = "{% capture foo = \"foo\" %}Lorem Ipsum{% endcapture %}";

        $stream = $this->environment->tokenize(new Source($source, 'index'));
        $this->parser->parse($stream);
        $this->expectExceptionMessage("Incorrect attribute name. Possible attribute names are: 'name', 'assign' and 'append'");
    }
}
