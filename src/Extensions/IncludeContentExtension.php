<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\Twig\TokenParser\IncludeContentTokenParser;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

class IncludeContentExtension extends AbstractExtension
{
    public function __construct(private ContentFactory $contentFactory)
    {
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

        return $content->oxcontents__oxcontent->value;
    }
}
