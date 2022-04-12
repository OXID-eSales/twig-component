<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateNameResolverInterface;

/**
 * Class TemplateNameResolver
 */
class TemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * @var TemplateNameResolverInterface
     */
    private $resolver;

    /**
     * TemplateNameResolver constructor.
     *
     * @param TemplateNameResolverInterface $resolver
     */
    public function __construct(TemplateNameResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolve(string $name): string
    {
        return $this->resolver->resolve($this->getFileNameWithoutExtension($name));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFileNameWithoutExtension(string $fileName): string
    {
        $fileName = $this->stripExtension($fileName, '.tpl');
        return $this->stripExtension($fileName, '.html.twig');
    }

    /**
     * @param string $fileName
     * @param string $extension
     *
     * @return string
     */
    private function stripExtension(string $fileName, string $extension): string
    {
        $pos = strrpos($fileName, $extension);
        if (false !== $pos) {
            $fileName = substr($fileName, 0, $pos);
        }

        return $fileName;
    }
}
