<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\TemplateType;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ModuleTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\ShopTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class TemplateOriginResolver implements TemplateOriginResolverInterface
{
    public function getTemplateOrigin(TemplateTypeInterface $template): TemplateTypeInterface
    {
        if ($template->isShopExtensionTemplate()) {
            $originalTemplate =  new ShopTemplateType($template->getName());
        } elseif ($template->isModuleExtensionTemplate()) {
            $originalTemplate =  new ModuleTemplateType($template->getName(), $template->getParentNamespace());
        } else {
            $originalTemplate =  $template;
        }
        return $originalTemplate;
    }
}
