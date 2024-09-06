<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Extensions\IfContentExtension;
use OxidEsales\Twig\Node\IfContentNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;

final class IfContentNodeTest extends AbstractOxidTwigTestCase
{
    public function testConstructor(): void
    {
        $body = new TextNode('Lorem Ipsum', 1);
        $variable = new AssignNameExpression('foo', 1);
        $node = new IfContentNode($body, [], $variable, 1);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($variable, $node->getNode('variable'));

        $expr = new ConstantExpression("oxsomething", 1);

        $node = new IfContentNode($body, ['ident' => $expr], $variable, 1);
        $this->assertEquals($expr, $node->getNode('ident'));
        $this->assertFalse($node->hasNode('oxid'));

        $node = new IfContentNode($body, ['oxid' => $expr], $variable, 1);
        $this->assertEquals($expr, $node->getNode('oxid'));
        $this->assertFalse($node->hasNode('ident'));
    }

    public static function getOxidTwigTests(): array
    {
        $ifContentExtensionClass = IfContentExtension::class;

        $tests = [];

        $body = new TextNode('Lorem Ipsum', 1);
        $variable = new AssignNameExpression('foo', 1);
        $expr = new ConstantExpression("oxsomething", 1);
        $node = new IfContentNode($body, ['ident' => $expr], $variable, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$context["foo"] = \$this->extensions['$ifContentExtensionClass']->getContent("oxsomething", null);
if(\$context["foo"]) { 
echo "Lorem Ipsum";
 } 
unset(\$context["foo"]);
EOF
        ];

        $node = new IfContentNode($body, ['oxid' => $expr], $variable, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$context["foo"] = \$this->extensions['$ifContentExtensionClass']->getContent(null, "oxsomething");
if(\$context["foo"]) { 
echo "Lorem Ipsum";
 } 
unset(\$context["foo"]);
EOF
        ];


        return $tests;
    }
}
