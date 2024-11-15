<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Node\HasRightsNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

final class HasRightsNodeTest extends NodeTestCase
{
    private const EXPECTED_NON_NESTED_NODE_SOURCE = <<<EOF
// line 1
yield "Lorem Ipsum";
EOF;
    private const EXPECTED_NESTED_NODE_SOURCE = <<<EOF
// line 1
// line 2
yield "Top";
// line 3
// line 4
yield "Inner";
// line 6
yield "Bottom";
EOF;

    #[DataProvider('compileDataProvider')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }

    public static function compileDataProvider(): array
    {
        return [
            [
                new HasRightsNode(
                    body: new TextNode(data: 'Lorem Ipsum', lineno: 1),
                    parameters: new ArrayExpression(
                        elements: [
                            new ConstantExpression(value: 'name', lineno: 1),
                            new ConstantExpression(value: 'value', lineno: 1)
                        ],
                        lineno: 1
                    ),
                    lineno: 1
                ),
                self::EXPECTED_NON_NESTED_NODE_SOURCE,
            ],
            [
                new HasRightsNode(
                    body: new Node(
                        nodes: [
                            new TextNode(data: 'Top', lineno: 2),
                            new HasRightsNode(
                                new TextNode(data: 'Inner', lineno: 4),
                                new ArrayExpression(
                                    elements: [
                                        new ConstantExpression(value: 'type', lineno: 3),
                                        new ConstantExpression(value: 'inner', lineno: 3)
                                    ],
                                    lineno: 3
                                ),
                                3
                            ),
                            new TextNode(data: 'Bottom', lineno: 6)
                        ]
                    ),
                    parameters: new ArrayExpression(
                        elements: [
                            new ConstantExpression(value: 'type', lineno: 1),
                            new ConstantExpression(value: 'outer', lineno: 1)
                        ],
                        lineno: 1
                    ),
                    lineno: 1
                ),
                self::EXPECTED_NESTED_NODE_SOURCE,
            ]
        ];
    }
}
