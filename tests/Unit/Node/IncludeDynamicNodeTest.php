<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Node;

use OxidEsales\Twig\Extensions\IncludeExtension;
use OxidEsales\Twig\Node\IncludeDynamicNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Test\NodeTestCase;

use function sprintf;

final class IncludeDynamicNodeTest extends NodeTestCase
{
    private const EXPECTED_ACTIVE_CONTEXT_EMPTY_PARAMETERS_NODE_SOURCE = <<<EOF
// line 1
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['%s']->renderForCache(['file' => "foo.twig"]);
} else {
\$this->load("foo.twig", 1)->display(\$context);
}
EOF;
    private const EXPECTED_ACTIVE_CONTEXT_NODE_SOURCE = <<<EOF
// line 1
\$parameters = ["foo" => true];
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['%s']->renderForCache(array_merge(\$parameters, ['file' => "foo.twig"]));
} else {
    \$parameters = \$this->extensions['%s']->includeDynamicPrefix(\$parameters);
\$this->load("foo.twig", 1)->display(array_merge(\$context, \$parameters));
}
EOF;
    private const EXPECTED_LOCAL_CONTEXT_NODE_SOURCE = <<<EOF
// line 1
\$parameters = ["foo" => true];
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['%s']->renderForCache(array_merge(\$parameters, ['file' => "foo.twig"]));
} else {
    \$parameters = \$this->extensions['%s']->includeDynamicPrefix(\$parameters);
\$this->load("foo.twig", 1)->display(\$parameters);
}
EOF;
    private const EXPECTED_LOCAL_CONTEXT_IGNORE_MISSING_NODE_SOURCE = <<<EOF
// line 1
try {
    \$parameters = ["foo" => true];
    if (!empty(\$context["_render4cache"])) {
        echo \$this->extensions['%s']->renderForCache(array_merge(\$parameters, ['file' => "foo.twig"]));
    } else {
        \$parameters = \$this->extensions['%s']->includeDynamicPrefix(\$parameters);
\$this->load("foo.twig", 1)->display(\$parameters);
    }
} catch (\Twig\Error\LoaderError \$e) {
    // ignore missing template
}
EOF;

    public static function compileDataProvider(): array
    {
        $expression = new ConstantExpression(value: 'foo.twig', lineno: 1);
        $variables = new ArrayExpression(
            elements: [
                new ConstantExpression(value: 'foo', lineno: 1),
                new ConstantExpression(value: true, lineno: 1)
            ],
            lineno: 1
        );
        return [
            [
                new IncludeDynamicNode(
                    expr: $expression,
                    variables: null,
                    only: false,
                    ignoreMissing: false,
                    lineno: 1
                ),
                sprintf(
                    self::EXPECTED_ACTIVE_CONTEXT_EMPTY_PARAMETERS_NODE_SOURCE,
                    IncludeExtension::class
                ),
            ],
            [
                new IncludeDynamicNode(
                    expr: $expression,
                    variables: $variables,
                    only: false,
                    ignoreMissing: false,
                    lineno: 1
                ),
                sprintf(
                    self::EXPECTED_ACTIVE_CONTEXT_NODE_SOURCE,
                    IncludeExtension::class,
                    IncludeExtension::class
                ),
            ],
            [
                new IncludeDynamicNode(
                    expr: $expression,
                    variables: $variables,
                    only: true,
                    ignoreMissing: false,
                    lineno: 1
                ),
                sprintf(
                    self::EXPECTED_LOCAL_CONTEXT_NODE_SOURCE,
                    IncludeExtension::class,
                    IncludeExtension::class
                ),
            ],
            [
                new IncludeDynamicNode(
                    expr: $expression,
                    variables: $variables,
                    only: true,
                    ignoreMissing: true,
                    lineno: 1
                ),
                sprintf(
                    self::EXPECTED_LOCAL_CONTEXT_IGNORE_MISSING_NODE_SOURCE,
                    IncludeExtension::class,
                    IncludeExtension::class
                ),
            ],
        ];
    }

    #[DataProvider('compileDataProvider')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }
}
