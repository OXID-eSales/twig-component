<?php

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Provider\NamespaceHierarchyProviderInterface;
use Twig\Loader\FilesystemLoader;

class TemplateHierarchyResolver implements TemplateHierarchyResolverInterface
{
    /** @var NamespaceHierarchyProviderInterface */
    private $namespaceHierarchyProvider;

    public function __construct(NamespaceHierarchyProviderInterface $namespaceHierarchyProvider)
    {
        $this->namespaceHierarchyProvider = $namespaceHierarchyProvider;
    }

    /** @inheritDoc */
    public function getParentForTemplate(string $templateName, string $ancestorTemplateName): string
    {
        if (!$this->hasHierarchy() || $this->lastInHierarchy($templateName)) {
            return $ancestorTemplateName;
        }

        return "@{$this->getParentNamespace($templateName)}/{$this->extractName($templateName)}";
    }

    private function lastInHierarchy(string $templateName): bool
    {
        return $this->hasNamespace($templateName)
            && $this->extractNamespace($templateName) === FilesystemLoader::MAIN_NAMESPACE;
    }

    private function hasHierarchy(): bool
    {
        return !empty($this->namespaceHierarchyProvider->getHierarchyAscending());
    }

    private function getParentNamespace(string $templateName): string
    {
        $namespaceHierarchy = $this->namespaceHierarchyProvider->getHierarchyAscending();
        $currentPosition = !$this->hasNamespace($templateName)
            ? 0
            : array_search($this->extractNamespace($templateName), $namespaceHierarchy, true);

        return $namespaceHierarchy[++$currentPosition] ?? FilesystemLoader::MAIN_NAMESPACE;
    }


    /**
     * @todo extract to a separate class
     * @param string $name
     * @return bool
     */
    private function hasNamespace(string $name): bool
    {
        return $name[0] === '@';
    }

    /**
     * @todo extract to a separate class
     * @param string $name
     * @return string
     */
    private function extractNamespace(string $name): string
    {
        $parts = explode('/', $name);
        return ltrim($parts[0], '@');
    }

    /**
     * @todo extract to a separate class
     * @param string $name
     * @return string
     */
    private function extractName(string $name): string
    {
        if (!$this->hasNamespace($name)) {
            return $name;
        }
        $parts = explode('/', $name);
        unset($parts[0]);
        return implode('/', $parts);
    }
}
