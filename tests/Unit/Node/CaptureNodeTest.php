<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Node\CaptureNode;
use Twig\Node\TextNode;

final class CaptureNodeTest extends AbstractOxidTwigTestCase
{
    public static function getOxidTwigTests(): array
    {
        return array_merge(
            self::getTestForCaptureWithAttributeName(),
            self::getTestForCaptureWithAttributeAssign(),
            self::getTestForCaptureWithAttributeAppend()
        );
    }

    private static function getTestForCaptureWithAttributeName(): array
    {
        $tests = [];
        $nodeForCaptureName = self::getCaptureNode('name');
        $tests[] = [$nodeForCaptureName, <<<EOF
// line 1
ob_start();
echo "Lorem Ipsum";
\$captureContent = ob_get_clean();
\$context['twig']['capture']['foo'] = \$captureContent;
unset(\$captureContent);
EOF
        ];

        return $tests;
    }

    private static function getTestForCaptureWithAttributeAssign(): array
    {
        $tests = [];
        $nodeForCaptureAssign = self::getCaptureNode('assign');
        $tests[] = [$nodeForCaptureAssign, <<<EOF
// line 1
ob_start();
echo "Lorem Ipsum";
\$captureContent = ob_get_clean();
if ('foo' != '') {
\$context['foo'] = \$captureContent;
}
unset(\$captureContent);
EOF
        ];

        return $tests;
    }

    private static function getTestForCaptureWithAttributeAppend(): array
    {
        $tests = [];
        $nodeForCapture = self::getCaptureNode('append');
        $tests[] = [$nodeForCapture, <<<EOF
// line 1
ob_start();
echo "Lorem Ipsum";
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
EOF
        ];

        return $tests;
    }

    private static function getCaptureNode(string $attributeName): CaptureNode
    {
        return new CaptureNode(
            $attributeName,
            'foo',
            new TextNode("Lorem Ipsum", 1),
            1,
            'capture'
        );
    }
}
