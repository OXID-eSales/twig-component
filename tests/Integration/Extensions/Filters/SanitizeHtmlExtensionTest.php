<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Html\HtmlSanitizerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Twig\Tests\Integration\Extensions\AbstractExtensionTestCase;

final class SanitizeHtmlExtensionTest extends AbstractExtensionTestCase
{
    use ContainerTrait;

    private string $unsafeHtml = '<div><script> alert("SPAM MESSAGE") </script></div>';
    private string $safeHtml = '<div></div>';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createContainer();
    }

    public function testSanitizerShouldEliminateUnsafeTags(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);
        $this->attachContainerToContainerFactory();
        $this->extension = new SanitizeHtmlExtension(ContainerFacade::get(HtmlSanitizerInterface::class));
        $template = "{{ '" . $this->unsafeHtml . "' | sanitize_html }}";

        $result = $this->getTemplate($template)->render([]);

        $this->assertEquals($this->safeHtml, $result);
    }

    public function testSanitizerShouldPassEverythingWhenDisabled(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', false);
        $this->attachContainerToContainerFactory();
        $this->extension = new SanitizeHtmlExtension(ContainerFacade::get(HtmlSanitizerInterface::class));
        $template = "{{ '" . $this->unsafeHtml . "' | sanitize_html }}";

        $result = $this->getTemplate($template)->render([]);

        $this->assertEquals($this->unsafeHtml, $result);
    }
}
