<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Unit\Resolver\TemplateChain\TemplateHandler\ModuleExtensions;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ChainAppenderInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateHandler\ModuleExtensions\ShopTemplateChainAppender;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use OxidEsales\Twig\TwigContextInterface;
use PHPUnit\Framework\TestCase;

final class ShopTemplateChainAppenderTest extends TestCase
{
    private const TEMPLATE = 'page.html.twig';
    private const MODULE_NAMESPACE = 'testModule';
    private const ACTIVE_THEME_ID = 'activeTheme';
    private const ADMIN_UI_NAME = 'adminUi';

    public function testStorefrontExtensionIsReadFromActiveThemeFolder(): void
    {
        $appender = $this->createAppender(
            isAdmin: false,
            existingExtensionFolders: [self::ACTIVE_THEME_ID, 'default']
        );

        $this->assertSame(
            '@testModule/extensions/themes/activeTheme/page.html.twig',
            $this->getAppendedTemplateName($appender)
        );
    }

    public function testAdminExtensionIsReadFromAdminUiFolder(): void
    {
        $appender = $this->createAppender(
            isAdmin: true,
            existingExtensionFolders: [self::ACTIVE_THEME_ID, self::ADMIN_UI_NAME, 'default']
        );

        $this->assertSame(
            '@testModule/extensions/themes/adminUi/page.html.twig',
            $this->getAppendedTemplateName($appender)
        );
    }

    public function testAdminExtensionFallsBackToDefaultFolder(): void
    {
        $appender = $this->createAppender(
            isAdmin: true,
            existingExtensionFolders: [self::ACTIVE_THEME_ID, 'default']
        );

        $this->assertSame(
            '@testModule/extensions/themes/default/page.html.twig',
            $this->getAppendedTemplateName($appender)
        );
    }

    private function getAppendedTemplateName(ShopTemplateChainAppender $appender): string
    {
        return $appender->addToChain(
            new TemplateChain(),
            new ShopTemplateType(self::TEMPLATE),
            new NamespacedDirectory(self::MODULE_NAMESPACE, '/module/views/twig')
        )->getLastChild()->getFullyQualifiedName();
    }

    /** @param string[] $existingExtensionFolders */
    private function createAppender(bool $isAdmin, array $existingExtensionFolders): ShopTemplateChainAppender
    {
        $twigContext = $this->createStub(TwigContextInterface::class);
        $twigContext->method('getActiveThemeId')->willReturn(self::ACTIVE_THEME_ID);
        $context = $this->createStub(ContextInterface::class);
        $context->method('isAdmin')->willReturn($isAdmin);

        return new ShopTemplateChainAppender(
            $twigContext,
            $this->createChainAppender($existingExtensionFolders),
            $context,
            self::ADMIN_UI_NAME,
        );
    }

    /** @param string[] $existingExtensionFolders */
    private function createChainAppender(array $existingExtensionFolders): ChainAppenderInterface
    {
        $existingFiles = array_map(
            static fn (string $folder): string => "extensions/themes/$folder/" . self::TEMPLATE,
            $existingExtensionFolders
        );
        $chainAppender = $this->createStub(ChainAppenderInterface::class);
        $chainAppender->method('addToChain')->willReturnCallback(
            static function (
                TemplateChain $chain,
                TemplateTypeInterface $template,
            ) use ($existingFiles): TemplateChain {
                if (in_array($template->getRelativeFilePath(), $existingFiles, true)) {
                    $chain->append($template);
                }

                return $chain;
            }
        );

        return $chainAppender;
    }
}
