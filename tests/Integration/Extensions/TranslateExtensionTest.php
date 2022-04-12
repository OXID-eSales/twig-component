<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use OxidEsales\Twig\Extensions\TranslateExtension;
use PHPUnit\Framework\TestCase;

class TranslateExtensionTest extends AbstractExtensionTest
{
    /**
     * @var TranslateExtension
     */
    private $translateExtension;

    protected function setUp(): void
    {
        $this->setLanguage(0);
        $translateFunctionLogic = new TranslateFunctionLogic();
        $this->translateExtension = new TranslateExtension($translateFunctionLogic);
        parent::setUp();
    }

    public function dataProvider()
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
     * @covers       \OxidEsales\Twig\Extensions\TranslateExtension::translate
     */
    public function testTranslate($params, $expectedTranslation)
    {
        $actualTranslation = $this->translateExtension->translate($params);
        $this->assertEquals($actualTranslation, $expectedTranslation);
    }
}
