<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Loader;

use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\Twig\Loader\CmsLoader;
use OxidEsales\Twig\Loader\CmsTemplateNameParser;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

final class CmsLoaderTest extends TestCase
{
    private CmsLoader $contentTemplateLoader;

    public function setUp(): void
    {
        parent::setUp();

        $validContent = $this->prepareContentStub(
            0,
            ['oxactive' => true, 'oxcontent' => "Template code (DE)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $englishContent = $this->prepareContentStub(
            1,
            ['oxactive' => true, 'oxcontent' => "Template code (EN)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $fieldContent = $this->prepareContentStub(
            0,
            [
                'oxactive' => true,
                'customfield' => "Template code (custom field)",
                'oxtimestamp' => '2018-10-09 09:32:06'
            ]
        );

        $notFreshContent = $this->prepareContentStub(
            0,
            ['oxactive' => true, 'oxtimestamp' => '2018-10-09 09:40:25']
        );

        $notValidContent = $this->prepareContentStub(0, ['oxactive' => false]);

        $contentFactoryStub = $this->createStub(ContentFactory::class);

        $contentFactoryStub
            ->method('getContent')
            ->willReturnMap(
                [
                    ['ident', 'valid', $validContent],
                    ['oxid', 'english', $englishContent],
                    ['ident', 'field', $fieldContent],
                    ['oxid', 'notFresh', $notFreshContent],
                    ['ident', 'notValid', $notValidContent]
                ]
            );

        /** @var ContentFactory $contentFactoryStub */
        $this->contentTemplateLoader = new CmsLoader(new CmsTemplateNameParser(), $contentFactoryStub);
    }

    /**
     * @throws LoaderError
     */
    public function testGetSourceContext(): void
    {
        $this->assertEquals(
            "Template code (DE)",
            $this->contentTemplateLoader->getSourceContext('content::ident::valid')->getCode()
        );

        $this->assertEquals(
            "Template code (EN)",
            $this->contentTemplateLoader->getSourceContext('content::oxid::english')->getCode()
        );

        $this->assertEquals(
            "Template code (custom field)",
            $this->contentTemplateLoader->getSourceContext('content::ident::field?field=customfield')->getCode()
        );
    }

    /**
     * testExists
     */
    public function testExists(): void
    {
        $this->assertTrue($this->contentTemplateLoader->exists('content::ident::valid'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::oxid::english'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::ident::field?field=customfield'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::oxid::notFresh'));

        $this->assertFalse($this->contentTemplateLoader->exists('invalidName'));
    }

    /**
     * @throws LoaderError
     */
    public function testIsFresh(): void
    {
        $time = strtotime('2018-10-09 09:37:16');
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::ident::valid', $time));
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::oxid::english', $time));
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::ident::field?field=customfield', $time));

        $this->assertFalse($this->contentTemplateLoader->isFresh('content::oxid::notFresh', $time));
    }

    /**
     * @throws LoaderError
     */
    public function testGetCacheKey(): void
    {
        $this->assertEquals(
            'content::ident::valid(0)',
            $this->contentTemplateLoader->getCacheKey('content::ident::valid')
        );

        $this->assertEquals(
            'content::oxid::english(1)',
            $this->contentTemplateLoader->getCacheKey('content::oxid::english')
        );

        $this->assertEquals(
            'content::ident::field?field=customfield(0)',
            $this->contentTemplateLoader->getCacheKey('content::ident::field?field=customfield')
        );
    }

    private function prepareContentStub(int $language, array $fields): Content
    {
        $contentStub = $this->createStub(Content::class);
        $contentStub->method('getLanguage')->willReturn($language);

        $fieldValues = [];
        foreach ($fields as $field => $value) {
            $fieldName = 'oxcontents__' . $field;
            $fieldValues[$fieldName] = (object) ['value' => $value];
        }

        $contentStub->method('__get')
            ->willReturnCallback(
                static fn(string $fieldName): ?object => $fieldValues[$fieldName] ?? null
            );

        return $contentStub;
    }
}
