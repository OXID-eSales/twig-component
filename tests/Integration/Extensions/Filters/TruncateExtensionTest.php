<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TruncateLogic;
use OxidEsales\Twig\Extensions\Filters\TruncateExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Extension\AbstractExtension;

final class TruncateExtensionTest extends AbstractExtensionTestCase
{
    protected AbstractExtension $extension;

    public function setUp(): void
    {
        $this->extension = new TruncateExtension(new TruncateLogic());
        parent::setUp();
    }

    #[DataProvider('truncateProvider')]
    public function testTruncate(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function truncateProvider(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. " .
                "Suspendisse dapibus pulvinar sem vitae.'|truncate }}",
                'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse...'
            ],
            [
                "{{ 'Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. " .
                "Suspendisse dapibus pulvinar sem vitae.'|truncate }}",
                'Duis iaculis &amp;#039;pellentesque&amp;#039; felis, et &amp;quot;pulvinar&amp;quot; ' .
                'elit lacinia at. Suspendisse...'
            ],
        ];
    }

    #[DataProvider('truncateProviderWithLength')]
    public function testTruncateWithLength(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function truncateProviderWithLength(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit.'|truncate(20) }}",
                'Duis iaculis...'
            ],
            [
                "{{ 'Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit.'|truncate(20) }}",
                'Duis iaculis...'
            ],
            [
                "{{ '&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, " .
                "et &quot;pulvinar&quot; elit.'|truncate(20) }}",
                '&amp;#039;Duis&amp;#039; &amp;#039;iaculis&amp;#039;...'
            ],
        ];
    }

    #[DataProvider('truncateProviderWithSuffix')]
    public function testTruncateWithSuffix(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function truncateProviderWithSuffix(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. " .
                "Suspendisse dapibus pulvinar sem vitae.'|truncate(80, ' (...)') }}",
                'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse (...)'
            ],
        ];
    }

    #[DataProvider('truncateProviderWithBreakWords')]
    public function testTruncateWithBreakWords(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function truncateProviderWithBreakWords(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. " .
                "Suspendisse dapibus pulvinar sem vitae.'|truncate(80, '...', true) }}",
                'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dap...'
            ],
        ];
    }
}
