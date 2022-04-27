<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\Twig\Resolver\ModulesTemplateDirectoryResolverInterface;
use OxidEsales\Twig\TwigContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ModulesTemplateChain implements TemplateChainInterface
{
    public function __construct(
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private ModulesTemplateDirectoryResolverInterface $moduleTemplateDirectoryResolver,
        private TwigContextInterface $twigContext,
        private Filesystem $filesystem
    ) {
    }

    /** @inheritDoc */
    public function getChain(string $templateName): array
    {
        $templateChain = [];
        foreach ($this->activeModulesDataProvider->getModuleIds() as $moduleId) {
            if ($this->moduleHasTemplateForActiveTheme($moduleId, $templateName)) {
                $templateChain[] = "@$moduleId/{$this->twigContext->getActiveThemeId()}/$templateName";
            } elseif ($this->moduleHasTemplate($moduleId, $templateName)) {
                $templateChain[] = "@$moduleId/$templateName";
            }
        }
        return $templateChain;
    }

    private function getAbsoluteTemplatePathForTheme(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $this->twigContext->getActiveThemeId(),
            $templateName
        );
    }

    private function getAbsoluteTemplatePath(string $moduleId, string $templateName): string
    {
        return Path::join(
            $this->moduleTemplateDirectoryResolver->getAbsolutePath($moduleId),
            $templateName
        );
    }

    private function moduleHasTemplateForActiveTheme(string $moduleId, string $templateName): bool
    {
        return $this->filesystem->exists($this->getAbsoluteTemplatePathForTheme($moduleId, $templateName));
    }

    private function moduleHasTemplate(string $moduleId, string $templateName): bool
    {
        return $this->filesystem->exists($this->getAbsoluteTemplatePath($moduleId, $templateName));
    }
}
