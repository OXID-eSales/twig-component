<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Node\HasRightsNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

final class HasRightsNodeTest extends NodeTestCase
{
    public function testConstruct(): void
    {
        $parameters = [];
        $parameters[] = new ConstantExpression('name', 1);
        $parameters[] = new ConstantExpression('value', 1);

        $body = new TextNode("Lorem Ipsum", 1);
        $parametersNode = new ArrayExpression($parameters, 1);
        $node = new HasRightsNode($body, $parametersNode, 1);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($parametersNode, $node->getNode('parameters'));
    }

    public function getTests(): array
    {
        return array_merge($this->getNonNestedBlocksTests(), $this->getNestedBlocksTests());
    }

    private function getNonNestedBlocksTests(): array
    {
        $tests = [];

        $parameters = [];
        $parameters[] = new ConstantExpression('name', 1);
        $parameters[] = new ConstantExpression('value', 1);

        $body = new TextNode("Lorem Ipsum", 1);
        $node = new HasRightsNode($body, new ArrayExpression($parameters, 1), 1);

        $tests[] = [$node, <<<EOF
// line 1
echo "Lorem Ipsum";
EOF
        ];

        return $tests;
    }

    private function getNestedBlocksTests(): array
    {
        $tests = [];

        $outerParameters = [];
        $outerParameters[] = new ConstantExpression('type', 1);
        $outerParameters[] = new ConstantExpression('outer', 1);

        $innerParameters = [];
        $innerParameters[] = new ConstantExpression('type', 3);
        $innerParameters[] = new ConstantExpression('inner', 3);

        $topBody = new TextNode("Top", 2);
        $innerBody = new TextNode("Inner", 4);
        $bottomBody = new TextNode("Bottom", 6);

        $innerHasRightsNode = new HasRightsNode($innerBody, new ArrayExpression($innerParameters, 3), 3);

        $outerBody = new Node([$topBody, $innerHasRightsNode, $bottomBody]);

        $node = new HasRightsNode($outerBody, new ArrayExpression($outerParameters, 1), 1);

        $tests[] = [$node, <<<EOF
// line 1
// line 2
echo "Top";
// line 3
// line 4
echo "Inner";
// line 6
echo "Bottom";
EOF
        ];

        return $tests;
    }
}
