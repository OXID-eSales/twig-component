<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;

class ModuleTemplateDirectoryResolver implements ModuleTemplateDirectoryResolverInterface
{
    /**
     * @var ModulePathResolverInterface
     */
    private $modulePathResolver;

    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(ModulePathResolverInterface $modulePathResolver, BasicContextInterface $context)
    {
        $this->modulePathResolver = $modulePathResolver;
        $this->context = $context;
    }

    public function getAbsolutePath(string $moduleId): string
    {
        return Path::join(
            $this->modulePathResolver->getFullModulePathFromConfiguration(
                $moduleId,
                $this->context->getDefaultShopId()
            ),
            '/views/twig/tpl/'
        );
    }
}
