<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateSalutationLogic;
use OxidEsales\Twig\Extensions\Filters\TranslateSalutationExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;

/**
 * Class TranslateSalutationExtensionTest
 */
class TranslateSalutationExtensionTest extends AbstractExtensionTest
{
    /** @var TranslateSalutationExtension */
    protected $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setLanguage(0);
        $this->extension = new TranslateSalutationExtension(new TranslateSalutationLogic());
    }

    public function translateSalutationProvider(): array
    {
        return [
            ["{{ 'MR'|translate_salutation }}", 'Herr'],
            ["{{ 'MRS'|translate_salutation }}", 'Frau'],
        ];
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @dataProvider translateSalutationProvider
     */
    public function testTranslateSalutation(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}