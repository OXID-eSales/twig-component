<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Twig\Extensions\SmartyCycleExtension;

final class SmartyCycleExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new SmartyCycleExtension();
    }

    /**
     * @dataProvider getStaticCycle
     */
    public function testStaticCycle(string $template, string $expected, array $variables = []): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    public function getStaticCycle(): array
    {
        return [
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "aba",
                ['values' => ["a", "b"]],
            ],
            [
                "{{ smarty_cycle(values, { name: \"cycleName\" }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { name: \"cycleName\" }) }}",
                "aab",
                ['values' => ["a", "b"]],
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { reset: true }) }}" .
                "{{ smarty_cycle(values) }}",
                "aab",
                ['values' => ["a", "b"]],
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle() }}" .
                "{{ smarty_cycle() }}",
                "aba",
                ['values' => ["a", "b"]],
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { advance: false }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "abba",
                ['values' => ["a", "b"]],
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { print: false }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "aab",
                ['values' => ["a", "b"]],
            ],
        ];
    }
}
