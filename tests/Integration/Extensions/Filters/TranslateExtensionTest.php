<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions\Filters;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use OxidEsales\Twig\Extensions\Filters\TranslateExtension;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTest;
use Twig\Extension\AbstractExtension;

final class TranslateExtensionTest extends AbstractExtensionTest
{
    protected AbstractExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new TranslateExtension(new TranslateFilterLogic());
    }

    /**
     * Provides data to testSimpleTranslating
     */
    public function simpleTranslatingProvider(): array
    {
        return [
            ["{{ 'FIRST_NAME'|translate }}", 0, 'Vorname'],
            ["{{ 'FIRST_NAME'|translate }}", 1, 'First name'],
            ["{{ 'VAT'|translate }}", 1, 'VAT']
        ];
    }

    /**
     * Tests simple translating, where only translation is fetched
     *
     *
     * @dataProvider simpleTranslatingProvider
     */
    public function testSimpleTranslating(string $template, int $languageId, string $expected)
    {
        $this->setLanguage($languageId);
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * Provides data to testTranslatingWithArguments
     */
    public function withArgumentsProvider(): array
    {
        return [
            ["{{ 'MANUFACTURER_S'|translate('Opel') }}", 0, '| Hersteller: Opel'],
            ["{{ 'MANUFACTURER_S'|translate('Opel') }}", 1, 'Manufacturer: Opel'],
            ["{{ 'INVITE_TO_SHOP'|translate(['Admin', 'OXID Shop']) }}", 0, 'Eine Einladung von Admin OXID Shop zu besuchen.'],
            ["{{ 'INVITE_TO_SHOP'|translate(['Admin', 'OXID Shop']) }}", 1, 'An invitation from Admin to visit OXID Shop']
        ];
    }

    /**
     * Tests value translating when translating strings containing %s
     *
     * @dataProvider withArgumentsProvider
     */
    public function testTranslatingWithArguments(string $template, int $languageId, string $expected)
    {
        $this->setLanguage($languageId);
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * Provides data to testTranslateFrontend_isMissingTranslation
     */
    public function missingTranslationProviderFrontend(): array
    {
        return [
            [true, "{{ 'MY_MISING_TRANSLATION'|translate }}", 'MY_MISING_TRANSLATION'],
            [false, "{{ 'MY_MISING_TRANSLATION'|translate }}", 'ERROR: Translation for MY_MISING_TRANSLATION not found!'],
        ];
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation(bool $isProductiveMode, string $template, string $expected)
    {
        $this->setAdminMode(false);
        $this->setLanguage(1);

        $oShop = Registry::getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new Field($isProductiveMode);
        $oShop->save();

        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * Provides data to testTranslateAdmin_isMissingTranslation
     */
    public function missingTranslationProviderAdmin(): array
    {
        return [
            ["{{ 'MY_MISING_TRANSLATION'|translate }}", 'ERROR: Translation for MY_MISING_TRANSLATION not found!'],
        ];
    }

    /**
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdmin_isMissingTranslation(string $template, string $expected)
    {
        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }
}
