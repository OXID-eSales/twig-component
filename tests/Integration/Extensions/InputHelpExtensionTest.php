<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Adapter;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\InputHelpLogic;
use OxidEsales\Twig\Extensions\InputHelpExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class InputHelpExtensionTest extends AbstractExtensionTestCase
{
    private InputHelpExtension $inputHelpExtension;

    public function setUp(): void
    {
        parent::setUp();
        $inputHelpLogic = new InputHelpLogic();
        $this->inputHelpExtension = new InputHelpExtension($inputHelpLogic);
    }

    public static function getIdentProvider(): array
    {
        return [
            [null, 1, false, null],
            ['FIRST_NAME', 1, false, 'FIRST_NAME']
        ];
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\InputHelpExtension::getHelpId
     */
    #[DataProvider('getIdentProvider')]
    public function testGetIdent(?string $params, int $iLang, bool $blAdmin, ?string $expected): void
    {
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($expected, $this->inputHelpExtension->getHelpId($params));
    }

    public static function getHelpTextProvider(): array
    {
        return [
            [null, 1, false, null],
            ['FIRST_NAME', 1, false, 'First name'],
            ['FIRST_NAME', 0, false, 'Vorname'],
            ['VAT', 1, false, 'VAT'],
        ];
    }

    /**
     * @covers \OxidEsales\Twig\Extensions\InputHelpExtension::getHelpText
     */
    #[DataProvider('getHelpTextProvider')]
    public function testGetHelpText(?string $params, int $iLang, bool $blAdmin, ?string $expected): void
    {
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($expected, $this->inputHelpExtension->getHelpText($params));
    }
}
