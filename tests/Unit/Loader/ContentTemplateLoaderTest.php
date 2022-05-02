<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Loader;

use OxidEsales\EshopCommunity\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\Twig\Loader\CmsLoader;
use OxidEsales\Twig\TemplateLoaderNameParser;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

final class ContentTemplateLoaderTest extends TestCase
{
    private CmsLoader $contentTemplateLoader;
    private MockBuilder $contentMockBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->contentMockBuilder = $this->getMockBuilder(Content::class)->setMethods(['getLanguage']);

        $validContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'oxcontent' => "Template code (DE)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $englishContentMock = $this->prepareContentMock(
            1,
            ['oxactive' => true, 'oxcontent' => "Template code (EN)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $fieldContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'customfield' => "Template code (custom field)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $notFreshContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'oxtimestamp' => '2018-10-09 09:40:25']
        );

        $notValidContentMock = $this->prepareContentMock(0, ['oxactive' => false]);

        $contentFactoryMock = $this
            ->getMockBuilder(ContentFactory::class)
            ->setMethods(['getContent'])
            ->getMock();

        $contentFactoryMock
            ->method('getContent')
            ->will(
                $this->returnValueMap(
                    [
                        ['ident', 'valid', $validContentMock],
                        ['oxid', 'english', $englishContentMock],
                        ['ident', 'field', $fieldContentMock],
                        ['oxid', 'notFresh', $notFreshContentMock],
                        ['ident', 'notValid', $notValidContentMock]
                    ]
                )
            );

        /** @var ContentFactory $contentFactoryMock */
        $this->contentTemplateLoader = new CmsLoader(new TemplateLoaderNameParser(), $contentFactoryMock);
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

    private function prepareContentMock(int $language, array $fields): MockObject
    {
        $mock = $this->contentMockBuilder->getMock();
        $mock->method('getLanguage')->willReturn($language);

        foreach ($fields as $field => $value) {
            $fieldName = 'oxcontents__' . $field;
            $mock->$fieldName = (object) ['value' => $value];
        }

        return $mock;
    }
}
