<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Loader;

use OxidEsales\EshopCommunity\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ContentFactory;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * @deprecated This class will be removed in the future. Use {% include_content %} in templates.
 */
class CmsLoader implements LoaderInterface
{
    public function __construct(private CmsTemplateNameParser $nameParser, private ContentFactory $contentFactory)
    {
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return Source
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext($name): Source
    {
        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);
        $parameters = $this->nameParser->getParameters($name);

        $content = $this->getContent($name);

        if ($content) {
            $field = $parameters['field'] ?? "oxcontent";

            $property = 'oxcontents__' . $field;
            $code = $content->$property->value;
        } else {
            throw new LoaderError("Template with $key '$value' not found.");
        }

        return new Source($code, $name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey($name): string
    {
        $content = $this->getContent($name);

        return sprintf("%s(%s)", $name, $content->getLanguage());
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param int    $time Timestamp of the last modification time of the cached template
     *
     * @return bool true if the template is fresh, false otherwise
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh($name, $time): bool
    {
        $contentTime = strtotime($this->getContent($name)->oxcontents__oxtimestamp->value);

        return $time > $contentTime;
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool If the template source code is handled by this loader or not
     */
    public function exists($name): bool
    {
        if (!$this->nameParser->isValidName($name)) {
            return false;
        }

        $loaderName = $this->nameParser->getLoaderName($name);
        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);
        $content = $this->getContent($name);

        return $loaderName === 'content' && in_array($key, ['ident', 'oxid']) && $value && $content;
    }

    /**
     * @throws LoaderError
     */
    private function getContent(string $name): Content
    {
        if (!$this->nameParser->isValidName($name)) {
            throw new LoaderError("Cannot load template. Name is invalid.");
        }

        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);

        $content = $this->contentFactory->getContent($key, $value);

        if (!$content) {
            throw new LoaderError("Cannot load template from database.");
        }

        if (!$content->oxcontents__oxactive->value) {
            throw new LoaderError("Template is not active.");
        }

        return $content;
    }
}
