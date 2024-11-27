<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Extensions\IfContentExtension;
use OxidEsales\Twig\Node\IfContentNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

use function sprintf;

final class IfContentNodeTest extends NodeTestCase
{
    private const EXPECTED_IDENT_NODE_SOURCE = <<<EOF
// line 1
\$context["foo"] = \$this->extensions['%s']->getContent("oxsomething", null);
if(\$context["foo"]) { \nyield "Lorem Ipsum";\n } \nunset(\$context["foo"]);
EOF;
    private const EXPECTED_OXID_NODE_SOURCE = <<<EOF
// line 1
\$context["foo"] = \$this->extensions['%s']->getContent(null, "oxsomething");
if(\$context["foo"]) { \nyield "Lorem Ipsum";\n } \nunset(\$context["foo"]);
EOF;

    public static function compileDataProvider(): array
    {
        $body = new TextNode(data: 'Lorem Ipsum', lineno: 1);
        $referenceExpression = new ConstantExpression(value: 'oxsomething', lineno: 1);
        $variable = new AssignNameExpression(name: 'foo', lineno: 1);

        return [
            [
                new IfContentNode(
                    body: $body,
                    reference: ['ident' => $referenceExpression],
                    variable: $variable,
                    lineno: 1
                ),
                sprintf(self::EXPECTED_IDENT_NODE_SOURCE, IfContentExtension::class)
            ],
            [
                new IfContentNode(
                    $body,
                    ['oxid' => $referenceExpression],
                    $variable,
                    1
                ),
                sprintf(self::EXPECTED_OXID_NODE_SOURCE, IfContentExtension::class)
            ],
        ];
    }

    #[DataProvider('compileDataProvider')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }
}
