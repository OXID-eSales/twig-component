<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\TokenParser;

use OxidEsales\Twig\Extensions\HasRightsExtension;
use OxidEsales\Twig\Node\HasRightsNode;
use OxidEsales\Twig\TokenParser\HasRightsTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig\Parser;
use Twig\Source;
use Twig\Token;

final class HasRightsTokenParserTest extends TestCase
{
    private HasRightsTokenParser $hasRightsParser;

    protected function setUp(): void
    {
        $env = $this->getEnv();
        $parser = new Parser($env);
        $this->hasRightsParser = new HasRightsTokenParser(HasRightsNode::class);
        $this->hasRightsParser->setParser($parser);
        parent::setUp();
    }

    /**
     * @covers \OxidEsales\Twig\TokenParser\HasRightsTokenParser::getTag
     */
    public function testGetTag(): void
    {
        $this->assertEquals('hasrights', $this->hasRightsParser->getTag());
    }

    /**
     * @covers \OxidEsales\Twig\TokenParser\HasRightsTokenParser::decideMyTagFork
     */
    public function testDecideMyTagForkIncorrect(): void
    {
        $token = new Token(Token::TEXT_TYPE, 1, 1);
        $this->assertEquals(false, $this->hasRightsParser->decideMyTagFork($token));
    }

    /**
     * @covers \OxidEsales\Twig\TokenParser\HasRightsTokenParser::decideMyTagFork
     */
    public function testDecideMyTagForkCorrect(): void
    {
        $token = new Token(5, 'endhasrights', 1);
        $this->assertEquals(true, $this->hasRightsParser->decideMyTagFork($token));
    }

    /**
     * @covers \OxidEsales\Twig\TokenParser\HasRightsTokenParser::parse
     */
    public function testParse(): void
    {
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $env = new Environment($loader, array('cache' => false, 'autoescape' => false));
        $env->addExtension(new HasRightsExtension(new HasRightsTokenParser(HasRightsNode::class)));

        $stream = $env->parse(
            $env->tokenize(new Source('{% hasrights {\'id\' : \'1\'} %}{% endhasrights %}', 'index'))
        );
        $stream->compile((new Compiler($env))->reset());

        $tags = [];
        foreach ($env->getTokenParsers() as $tokenParser) {
            $tags[] = $tokenParser->getTag();
        }
        $extensions = $env->getExtensions();

        $this->assertContains('hasrights', $tags);
        $this->assertTrue(isset($extensions[HasRightsExtension::class]));
    }

    /**
     * @covers \OxidEsales\Twig\TokenParser\HasRightsTokenParser::parse
     */
    public function testParseException(): void
    {
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $env = new Environment($loader, ['cache' => false, 'autoescape' => false]);
        $env->addExtension(new HasRightsExtension(new HasRightsTokenParser(HasRightsNode::class)));

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('Unexpected "foo" tag (expecting closing tag for the "hasrights" tag defined near line 1) in "index" at line 1.');

        $env->parse($env->tokenize(new Source('{% hasrights {\'id\' : \'1\'} %}{% foo %}', 'index')));
    }

    private function getEnv(): Environment
    {
        $loader = new ArrayLoader(['tokens' => 'foo']);
        $env = new Environment($loader, ['debug' => false, 'cache' => false]);
        if (!$env->hasExtension('hasrights')) {
            $env->addExtension(new HasRightsExtension(new HasRightsTokenParser(HasRightsNode::class)));
            $env->addTokenParser(new HasRightsTokenParser(HasRightsNode::class));
        }

        return $env;
    }
}
