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
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader as TwigLoader;

/**
 * Class ContentSnippetLoader
 */
class FilesystemLoader extends TwigLoader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TemplateLoaderInterface
     */
    private $loader;

    /**
     * @var TemplateLoaderInterface
     */
    private $adminLoader;

    /**
     * FilesystemLoader constructor.
     *
     * @param array                   $paths
     * @param string|null             $rootPath
     * @param TemplateLoaderInterface $loader
     * @param TemplateLoaderInterface $adminLoader
     */
    public function __construct(
        $paths = [],
        string $rootPath = null,
        TemplateLoaderInterface $loader = null,
        TemplateLoaderInterface $adminLoader = null
    ) {
        parent::__construct($paths, $rootPath);

        $this->config = Registry::getConfig();
        $this->loader = $loader;
        $this->adminLoader = $adminLoader;
    }

    /**
     * @param string $name
     * @param bool   $throw
     *
     * @return string|null
     */
    public function findTemplate($name, $throw = true)
    {
        try {
            $template = parent::findTemplate($name, $throw);

            if ($template) {
                return $template;
            }
        } catch (LoaderError $error) {
        }

        if ($this->config->isAdmin()) {
            try{
               $template = $this->adminLoader->getPath($name);
            } catch (TemplateFileNotFoundException $e) {
                //let twig engine handle template loading and error throwing.
                return null;
            }
        } else {
            try {
                $template = $this->loader->getPath($name);
            } catch (TemplateFileNotFoundException $e) {
                //let twig engine handle template loading and error throwing.
                return null;
            }
        }

        if (!$template && isset($error)) {
            throw $error;
        } else {
            return $template;
        }
    }
}
