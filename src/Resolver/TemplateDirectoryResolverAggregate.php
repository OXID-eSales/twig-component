<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver;

class TemplateDirectoryResolverAggregate implements TemplateDirectoryResolverInterface
{
    /**
     * @param \OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface[] $directoryResolvers
     */
    public function __construct(
        private array $directoryResolvers
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->directoryResolvers as $key => $resolver) {
            $directories[$key] = $resolver->getTemplateDirectories();
        }

        return array_merge(...$directories);
    }
}
