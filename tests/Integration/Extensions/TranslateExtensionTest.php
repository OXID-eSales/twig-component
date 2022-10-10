<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Extensions\TranslateExtension;

final class TranslateExtensionTest extends AbstractExtensionTest
{
    use ContainerTrait;

    private TranslateExtension $translateExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setLanguage(0);
        $this->translateExtension = $this->get(TranslateExtension::class);
    }

    public function dataProvider(): array
    {
        return [
            [['ident' => 'foobar'], 'ERROR: Translation for foobar not found!'],
            [['ident' => 'foo', 'noerror' => true], 'foo'],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \OxidEsales\Twig\Extensions\TranslateExtension::translate
     */
    public function testTranslate(array $params, string $expectedTranslation): void
    {
        $actualTranslation = $this->translateExtension->translate($params);

        $this->assertEquals($expectedTranslation, $actualTranslation);
    }
}
