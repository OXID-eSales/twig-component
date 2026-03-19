<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Html\HtmlSanitizerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Extensions\IncludeContentExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class IncludeContentExtensionTest extends AbstractExtensionTestCase
{
    use ContainerTrait;

    private ContentFactory $contentFactoryStub;
    private string $spamContent = 'not spam<script>alert("spam")</script>';

    public function setUp(): void
    {
        parent::setUp();

        $this->contentFactoryStub = $this->createStub(ContentFactory::class);

        $this->contentFactoryStub
            ->method('getContent')
            ->willReturnMap($this->getContentMap());
    }

    public static function contentProvider(): array
    {
        return [
            ["{% include_content 'german' %}", "Template code (DE)"],
            ["{% include_content 'english' %}", "Template code (EN)"],
            ["{% include_content 'twig_code' with { my_var: 'my_val' } %}", "In my_var I have my_val value"],
            ["{% set content_name = 'dynamic_content' %}{% include_content content_name %}", "Dynamic content"],
            ["{% set content_name = 'spam' %}{% include_content content_name %}", "not spam"],
        ];
    }

    #[DataProvider('contentProvider')]
    public function testContent(string $template, string $expected): void
    {
        $this->initializeExtension(true);
        $result = $this->getTemplate($template)->render([]);
        $this->assertSame($expected, $result, "Unexpected rendered content for template: $template");
    }

    public function testNotActiveContent(): void
    {
        $this->initializeExtension(false);
        $this->expectException(LoaderError::class);
        $this->expectExceptionMessageMatches('/^Template is not active/');
        $this->getTemplate('{% include_content "not_active" %}')->render([]);
    }

    public function testContentShouldPassEverythingWhenSanitizerDisabled(): void
    {
        $this->initializeExtension(false);
        $template = "{% set content_name = 'spam' %}{% include_content content_name %}";

        $result = $this->getTemplate($template)->render([]);
        $this->assertSame($this->spamContent, $result);
    }

    public function testInactiveContentWithSanitizerEnabledStillThrows(): void
    {
        $this->initializeExtension(true);
        $this->expectException(LoaderError::class);
        $this->getTemplate("{% include_content 'not_active' %}")->render([]);
    }

    protected function getTemplate(string $template): Template
    {
        $twig = new Environment(new ArrayLoader(['index' => $template]), [
            'debug' => true,
            'cache' => false,
        ]);
        $twig->addExtension($this->extension);
        $twig->addExtension(new StringLoaderExtension());

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }

    private function initializeExtension(bool $sanitizerEnabled): void
    {
        $this->createContainer();
        $this->setParameter('oxid_esales.html_sanitizer_enabled', $sanitizerEnabled);

        $this->extension = new IncludeContentExtension(
            $this->contentFactoryStub,
            ContainerFacade::get(HtmlSanitizerInterface::class)
        );
    }

    private function prepareContent(int $language, array $fields): Content
    {
        $content = new Content();
        $content->setLanguage($language);

        foreach ($fields as $field => $value) {
            $property = 'oxcontents__' . $field;
            $content->$property = (object)['value' => $value];
        }

        return $content;
    }

    /** Build the content return map for the stub factory */
    private function getContentMap(): array
    {
        return [
            ['ident', 'german', $this->prepareContent(0, [
                'oxactive' => true,
                'oxcontent' => 'Template code (DE)',
            ])],
            ['ident', 'english', $this->prepareContent(1, [
                'oxactive' => true,
                'oxcontent' => 'Template code (EN)',
            ])],
            ['ident', 'twig_code', $this->prepareContent(0, [
                'oxactive' => true,
                'oxcontent' => 'In my_var I have {{ my_var }} value',
            ])],
            ['ident', 'dynamic_content', $this->prepareContent(0, [
                'oxactive' => true,
                'oxcontent' => 'Dynamic content',
            ])],
            ['ident', 'not_active', $this->prepareContent(0, [
                'oxactive' => false,
                'oxcontent' => 'Not active content',
            ])],
            ['ident', 'spam', $this->prepareContent(0, [
                'oxactive' => true,
                'oxcontent' => $this->spamContent,
            ])],
        ];
    }
}
