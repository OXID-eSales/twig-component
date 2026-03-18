<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Node\CaptureNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

final class CaptureNodeTest extends NodeTestCase
{
    private const EXPECTED_NAME_ATTRIBUTE_SOURCE = <<<EOF
// line 1
ob_start();
yield "Lorem Ipsum";
\$captureContent = ob_get_clean();
\$context['twig']['capture']['foo'] = \$captureContent;
unset(\$captureContent);
EOF;
    private const EXPECTED_ASSIGN_ATTRIBUTE_SOURCE = <<<EOF
// line 1
ob_start();
yield "Lorem Ipsum";
\$captureContent = ob_get_clean();
if ('foo' != '') {
\$context['foo'] = \$captureContent;
}
unset(\$captureContent);
EOF;
    private const EXPECTED_APPEND_ATTRIBUTE_SOURCE = <<<EOF
// line 1
ob_start();
yield "Lorem Ipsum";
\$captureContent = ob_get_clean();
if ('foo' != '' && isset(\$captureContent)) {
if (!isset(\$context['foo'])) {
\$context['foo'] = [];
}
if (!is_array(\$context['foo'])) {
\$context['foo'] = [\$context['foo']];
}
\$context['foo'][] = \$captureContent;
}
unset(\$captureContent);
EOF;

    #[DataProvider('compileDataProvider')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $this->assertNodeCompilation($source, $node);
    }

    public static function compileDataProvider(): array
    {
        $body = new TextNode(data: 'Lorem Ipsum', lineno: 1);

        return [
            [
                new CaptureNode(
                    attributeName: 'name',
                    variableName: 'foo',
                    body: $body,
                    line: 1,
                ),
                self::EXPECTED_NAME_ATTRIBUTE_SOURCE,
            ],
            [
                new CaptureNode(
                    attributeName: 'assign',
                    variableName: 'foo',
                    body: $body,
                    line: 1,
                ),
                self::EXPECTED_ASSIGN_ATTRIBUTE_SOURCE,
            ],
            [
                new CaptureNode(
                    attributeName: 'append',
                    variableName: 'foo',
                    body: $body,
                    line: 1,
                ),
                self::EXPECTED_APPEND_ATTRIBUTE_SOURCE,
            ]
        ];
    }
}
