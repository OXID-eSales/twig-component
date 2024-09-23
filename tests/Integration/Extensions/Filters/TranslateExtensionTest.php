<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\Twig\Extensions\Filters\TranslateExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;
use Twig\Extension\AbstractExtension;

final class TranslateExtensionTest extends AbstractExtensionTestCase
{
    protected AbstractExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $this->extension = $this->get(TranslateExtension::class);
    }

    public static function simpleTranslatingProvider(): array
    {
        return [
            ["{{ 'FIRST_NAME'|translate }}", 0, 'Vorname'],
            ["{{ 'FIRST_NAME'|translate }}", 1, 'First name'],
            ["{{ 'VAT'|translate }}", 1, 'VAT']
        ];
    }

    /**
     * @dataProvider simpleTranslatingProvider
     */
    public function testSimpleTranslating(string $template, int $languageId, string $expected): void
    {
        $this->setLanguage($languageId);
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function withArgumentsProvider(): array
    {
        return [
            ["{{ 'MANUFACTURER_S'|translate('Opel') }}", 0, '| Hersteller: Opel'],
            ["{{ 'MANUFACTURER_S'|translate('Opel') }}", 1, 'Manufacturer: Opel'],
            [
                "{{ 'INVITE_TO_SHOP'|translate(['Admin', 'OXID Shop']) }}",
                0,
                'Eine Einladung von Admin OXID Shop zu besuchen.',
            ],
            [
                "{{ 'INVITE_TO_SHOP'|translate(['Admin', 'OXID Shop']) }}",
                1,
                'An invitation from Admin to visit OXID Shop',
            ]
        ];
    }

    /**
     * @dataProvider withArgumentsProvider
     */
    public function testTranslatingWithArguments(string $template, int $languageId, string $expected): void
    {
        $this->setLanguage($languageId);
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function missingTranslationProviderFrontend(): array
    {
        return [
            [true, "{{ 'MY_MISSING_TRANSLATION'|translate }}", 'MY_MISSING_TRANSLATION'],
            [
                false,
                "{{ 'MY_MISSING_TRANSLATION'|translate }}",
                'ERROR: Translation for MY_MISSING_TRANSLATION not found!',
            ],
        ];
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontendIsMissingTranslation(
        bool $isProductiveMode,
        string $template,
        string $expected
    ): void {
        $this->setAdminMode(false);
        $this->setLanguage(1);

        $oShop = Registry::getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new Field($isProductiveMode);
        $oShop->save();

        $this->assertStringContainsString($expected, $this->getTemplate($template)->render([]));
    }

    public static function missingTranslationProviderAdmin(): array
    {
        return [
            ["{{ 'MY_MISSING_TRANSLATION'|translate }}", 'ERROR: Translation for MY_MISSING_TRANSLATION not found!'],
        ];
    }

    /**
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdminIsMissingTranslation(string $template, string $expected): void
    {
        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
