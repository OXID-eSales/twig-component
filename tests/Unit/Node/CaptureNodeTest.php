<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Node\CaptureNode;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

final class CaptureNodeTest extends NodeTestCase
{

    private $variableName = 'foo';
    private $line = 1;
    private $tag = 'capture';
    private $body;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->body = new TextNode("Lorem Ipsum", 1);
    }

    public function getTests()
    {
        return array_merge($this->getTestForCaptureWithAttributeName(), $this->getTestForCaptureWithAttributeAssign(), $this->getTestForCaptureWithAttributeAppend());
    }

    private function getTestForCaptureWithAttributeName()
    {
        $tests = [];
        $nodeForCaptureName = new CaptureNode('name', $this->variableName, $this->body, $this->line, $this->tag);

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

    private function getTestForCaptureWithAttributeAssign()
    {
        $nodeForCaptureAssign = new CaptureNode('assign', $this->variableName, $this->body, $this->line, $this->tag);
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

    private function getTestForCaptureWithAttributeAppend()
    {
        $nodeForCaptureAssign = new CaptureNode('append', $this->variableName, $this->body, $this->line, $this->tag);
        $tests[] = [$nodeForCaptureAssign, <<<EOF
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
}
