<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TruncateLogic;
use OxidEsales\Twig\Extensions\Filters\TruncateExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;

/**
 * Class TruncateExtensionTest
 */
class TruncateExtensionTest extends AbstractExtensionTest
{
    /** @var TruncateExtension */
    protected $extension;

    public function setUp(): void
    {
        $this->extension = new TruncateExtension(new TruncateLogic());
        parent::setUp();
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider truncateProvider
     */
    public function testTruncate(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @return array
     */
    public function truncateProvider(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.'|truncate }}",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse..."
            ],
            [
                "{{ 'Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse dapibus pulvinar sem vitae.'|truncate }}",
                "Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse..."
            ],
            [
                "{{ '&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse dapibus pulvinar sem vitae.'|truncate }}",
                "&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at...."
            ],
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider truncateProviderWithLength
     */
    public function testTruncateWithLength(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @return array
     */
    public function truncateProviderWithLength(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit.'|truncate(20) }}",
                "Duis iaculis..."
            ],
            [
                "{{ 'Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit.'|truncate(20) }}",
                "Duis iaculis..."
            ],
            [
                "{{ '&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit.'|truncate(20) }}",
                "&#039;Duis&#039; &#039;iaculis&#039;..."
            ],
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider truncateProviderWithSuffix
     */
    public function testTruncateWithSuffix(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @return array
     */
    public function truncateProviderWithSuffix(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.'|truncate(80, ' (...)') }}",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse (...)"
            ],
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider truncateProviderWithBreakWords
     */
    public function testTruncateWithBreakWords(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @return array
     */
    public function truncateProviderWithBreakWords(): array
    {
        return [
            [
                "{{ 'Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.'|truncate(80, '...', true) }}",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dap..."
            ],
        ];
    }
}
