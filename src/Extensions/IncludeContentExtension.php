<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Html\HtmlSanitizerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\Twig\TokenParser\IncludeContentTokenParser;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

class IncludeContentExtension extends AbstractExtension
{
    public function __construct(
        private ContentFactory $contentFactory,
        private ?HtmlSanitizerInterface $sanitizer = null
    ) {
        if ($this->sanitizer === null && ContainerFacade::has(HtmlSanitizerInterface::class)) {
            $this->sanitizer = ContainerFacade::get(HtmlSanitizerInterface::class);
        }
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new IncludeContentTokenParser()];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('content', [$this, 'content'])];
    }

    public function content(string $name): string
    {
        $content = $this->contentFactory->getContent('ident', $name);

        if (!$content) {
            throw new LoaderError("Cannot load template from database.");
        }

        if (!$content->oxcontents__oxactive->value) {
            throw new LoaderError("Template is not active.");
        }

        if (!$this->sanitizer) {
            return $content->oxcontents__oxcontent->value;
        }

        return $this->sanitizer->sanitize($content->oxcontents__oxcontent->value);
    }
}
