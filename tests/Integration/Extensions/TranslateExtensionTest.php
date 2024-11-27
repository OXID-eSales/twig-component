<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Twig\Extensions\TranslateExtension;
use PHPUnit\Framework\Attributes\DataProvider;

final class TranslateExtensionTest extends AbstractExtensionTestCase
{
    private TranslateExtension $translateExtension;

    public function setUp(): void
    {
        parent::setUp();

        $this->setLanguage(0);
        $this->translateExtension = $this->get(TranslateExtension::class);
    }

    public static function dataProvider(): array
    {
        return [
            [['ident' => 'foobar'], 'ERROR: Translation for foobar not found!'],
            [['ident' => 'foo', 'noerror' => true], 'foo'],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testTranslate(array $params, string $expectedTranslation): void
    {
        $actualTranslation = $this->translateExtension->translate($params);

        $this->assertEquals($expectedTranslation, $actualTranslation);
    }
}
