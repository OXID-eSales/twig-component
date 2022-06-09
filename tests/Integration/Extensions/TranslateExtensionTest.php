<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use OxidEsales\Twig\Extensions\TranslateExtension;

final class TranslateExtensionTest extends AbstractExtensionTest
{
    private TranslateExtension $translateExtension;

    protected function setUp(): void
    {
        $this->setLanguage(0);
        $translateFunctionLogic = new TranslateFunctionLogic();
        $this->translateExtension = new TranslateExtension($translateFunctionLogic);
        parent::setUp();
    }

    public function dataProvider(): array
    {
        return [
            [[], 'ERROR: Translation for IDENT MISSING not found!'],
            [['ident' => 'foobar'], 'ERROR: Translation for foobar not found!'],
            [['ident' => 'FIRST_NAME', 'suffix' => '_foo'], 'Vorname_foo'],
            [['ident' => 'foo', 'noerror' => true], 'foo'],
            [['ident' => 'foo', 'noerror' => 'bar'], 'foo']
        ];
    }

    /**
     * @param $params
     * @param $expectedTranslation
     *
     * @dataProvider dataProvider
     * @covers \OxidEsales\Twig\Extensions\TranslateExtension::translate
     */
    public function testTranslate($params, $expectedTranslation): void
    {
        $actualTranslation = $this->translateExtension->translate($params);
        $this->assertEquals($actualTranslation, $expectedTranslation);
    }
}
