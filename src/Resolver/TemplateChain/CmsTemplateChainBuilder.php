<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain;

use OxidEsales\Twig\Resolver\TemplateChain\DataObject\TemplateChain;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\CmsTemplateType;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;

class CmsTemplateChainBuilder implements TemplateChainBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function getChain(TemplateTypeInterface $templateType): TemplateChain
    {
        $chain = new TemplateChain();

        if ($templateType instanceof CmsTemplateType) {
            $chain->append($templateType);
        }

        return $chain;
    }
}
