<?php

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\Twig\Resolver\TemplateHierarchyResolverInterface;
use OxidEsales\Twig\TokenParser\ExtendsHierarchyTokenParser;
use Twig\Extension\AbstractExtension;

class InheritanceHierarchyExtension extends AbstractExtension
{
    /** @var TemplateHierarchyResolverInterface */
    private $templateHierarchyResolver;

    public function __construct(
        TemplateHierarchyResolverInterface $templateHierarchyResolver
    ) {
        $this->templateHierarchyResolver = $templateHierarchyResolver;
    }

    public function getTokenParsers(): array
    {
        return [
            new ExtendsHierarchyTokenParser(
                $this->templateHierarchyResolver
            )
        ];
    }
}
