<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Adapter;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\InputHelpLogic;
use OxidEsales\Twig\Extensions\InputHelpExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;
use PHPUnit\Framework\TestCase;

class InputHelpExtensionTest extends AbstractExtensionTest
{
    /**
     * @var InputHelpExtension
     */
    private $inputHelpExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $inputHelpLogic = new InputHelpLogic();
        $this->inputHelpExtension = new InputHelpExtension($inputHelpLogic);
    }

    /**
     * @return array
     */
    public function getIdentProvider()
    {
        return array(
            [null, 1, false, null],
            ['FIRST_NAME', 1, false, 'FIRST_NAME']
        );
    }

    /**
     * @param $params
     * @param $iLang
     * @param $blAdmin
     * @param $expected
     *
     * @dataProvider getIdentProvider
     * @covers       \OxidEsales\Twig\Extensions\InputHelpExtension::getHelpId
     */
    public function testGetIdent($params, $iLang, $blAdmin, $expected)
    {
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($expected, $this->inputHelpExtension->getHelpId($params));
    }

    /**
     * @return array
     */
    public function getHelpTextProvider()
    {
        return array(
            [null, 1, false, null],
            ['FIRST_NAME', 1, false, 'First name'],
            ['FIRST_NAME', 0, false, 'Vorname'],
            ['VAT', 1, false, 'VAT'],
        );
    }

    /**
     * @param $params
     * @param $iLang
     * @param $blAdmin
     * @param $expected
     *
     * @dataProvider getHelpTextProvider
     * @covers       \OxidEsales\Twig\Extensions\InputHelpExtension::getHelpText
     */
    public function testgetHelpText($params, $iLang, $blAdmin, $expected)
    {
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($expected, $this->inputHelpExtension->getHelpText($params));
    }

    /**
     * Sets language
     *
     * @param int $languageId
     */
    public function setLanguage($languageId)
    {
        $oxLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $oxLang->setBaseLanguage($languageId);
        $oxLang->setTplLanguage($languageId);
    }

}
