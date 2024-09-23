<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\Twig\Extensions\IncludeContentExtension;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class IncludeContentExtensionTest extends AbstractExtensionTestCase
{
    private MockBuilder $contentMockBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->contentMockBuilder = $this
            ->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLanguage']);

        $deContentMock = $this->prepareContentMock(0, [
            'oxactive' => true,
            'oxcontent' => 'Template code (DE)'
        ]);
        $enContentMock = $this->prepareContentMock(1, [
            'oxactive' => true,
            'oxcontent' => 'Template code (EN)'
        ]);
        $twigContentMock = $this->prepareContentMock(0, [
            'oxactive' => true,
            'oxcontent' => 'In my_var I have {{ my_var }} value'
        ]);
        $dynamicContentMock = $this->prepareContentMock(0, [
            'oxactive' => true,
            'oxcontent' => 'Dynamic content'
        ]);
        $notActiveContentMock = $this->prepareContentMock(0, [
            'oxactive' => false,
            'oxcontent' => 'Not active content'
        ]);

        /** @var MockObject|ContentFactory $contentFactoryMock */
        $contentFactoryMock = $this
            ->getMockBuilder(ContentFactory::class)
            ->onlyMethods(['getContent'])
            ->getMock();

        $contentFactoryMock
            ->method('getContent')
            ->willReturnMap([
                ['ident', 'german', $deContentMock],
                ['ident', 'english', $enContentMock],
                ['ident', 'twig_code', $twigContentMock],
                ['ident', 'dynamic_content', $dynamicContentMock],
                ['ident', 'not_active', $notActiveContentMock]
            ]);

        $this->extension = new IncludeContentExtension($contentFactoryMock);
    }

    /**
     * @dataProvider contentProvider
     */
    public function testContent(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    public static function contentProvider(): array
    {
        return [
            [
                "{% include_content 'german' %}",
                'Template code (DE)'
            ],
            [
                "{% include_content 'english' %}",
                'Template code (EN)'
            ],
            [
                "{% include_content 'twig_code' with { my_var: 'my_val' } %}",
                'In my_var I have my_val value'
            ],
            [
                "{% set content_name = 'dynamic_content' %}{% include_content content_name %}",
                'Dynamic content'
            ],
        ];
    }

    public function testNotActiveContent(): void
    {
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessageMatches('/^Template is not active/');
        $this->getTemplate('{% include_content "not_active" %}')->render([]);
    }

    protected function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);
        $twig->addExtension(new StringLoaderExtension());

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }

    private function prepareContentMock(int $language, array $fields): MockObject
    {
        $mock = $this->contentMockBuilder->getMock();
        $mock->method('getLanguage')->willReturn($language);

        foreach ($fields as $field => $value) {
            $fieldName = 'oxcontents__' . $field;
            $mock->$fieldName = (object)['value' => $value];
        }

        return $mock;
    }
}
