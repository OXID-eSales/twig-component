<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Exception\AdminUiDirectoryNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Loader\FilesystemLoader;

readonly class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    private const TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private Config $config,
        private ActiveThemeProviderInterface $activeThemeProvider,
        private AdminUiDirectoryResolverInterface $adminUiDirectoryResolver,
        private Filesystem $filesystem,
    ) {
    }

    /** @throws AdminUiDirectoryNotFoundException */
    public function getTemplateDirectories(): array
    {
        return array_map(
            static fn (string $directory) => new NamespacedDirectory(FilesystemLoader::MAIN_NAMESPACE, $directory),
            array_values(array_filter($this->getAreaTemplateDirectories()))
        );
    }

    /**
     * @return string[]
     * @throws AdminUiDirectoryNotFoundException
     */
    private function getAreaTemplateDirectories(): array
    {
        if ($this->config->isAdmin()) {
            return $this->getAdminUiTemplateDirectories();
        }

        return array_map($this->getTemplateDirectoryForTheme(...), $this->getActiveThemeIds());
    }

    /**
     * @return string[]
     * @throws AdminUiDirectoryNotFoundException
     */
    private function getAdminUiTemplateDirectories(): array
    {
        $templateDirectories = [];
        foreach ($this->adminUiDirectoryResolver->getDirectories() as $directory) {
            $templateDirectory = Path::join($directory, self::TEMPLATES_DIRECTORY_NAME);
            if ($this->filesystem->exists($templateDirectory)) {
                $templateDirectories[] = $templateDirectory;
            }
        }

        return $templateDirectories;
    }

    /** @return string[] */
    private function getActiveThemeIds(): array
    {
        try {
            $activeTheme = $this->activeThemeProvider->getActiveTheme();
        } catch (ActiveThemeNotFoundException) {
            return [];
        }

        return $activeTheme->hasParentTheme()
            ? [$activeTheme->getId(), $activeTheme->getParentThemeId()]
            : [$activeTheme->getId()];
    }

    private function getTemplateDirectoryForTheme(string $themeId): string
    {
        return (string)$this->config->getDir(
            null,
            self::TEMPLATES_DIRECTORY_NAME,
            false,
            null,
            null,
            $themeId,
            true,
            true
        );
    }
}
