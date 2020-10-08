<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Loader;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\TemplateFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader as TwigLoader;

/**
 * Class ContentSnippetLoader
 */
class FilesystemLoader extends TwigLoader
{
    /**
     * @var TemplateNameResolverInterface
     */
    private $templateNameResolver;

    public function __construct(
        $paths = [],
        TemplateNameResolverInterface $templateNameResolver
    ) {
        parent::__construct($paths);
        $this->templateNameResolver = $templateNameResolver;
    }

    /**
     * @param string $name
     * @param bool   $throw
     *
     * @return string|null
     */
    protected function findTemplate($name, $throw = true)
    {
        return parent::findTemplate($this->templateNameResolver->resolve($name), $throw);
    }
}
