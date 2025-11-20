<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Resolver\TemplateChain\DataObject;

use ArrayIterator;
use IteratorAggregate;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateType\DataObject\TemplateTypeInterface;
use Traversable;

use function array_splice;
use function count;
use function end;

class TemplateChain implements IteratorAggregate
{
    /**
     * @var TemplateTypeInterface[]
     */
    private array $chain = [];

    public function append(TemplateTypeInterface $templateType): void
    {
        $parentNamespace = $templateType->getParentNamespace();

        if ($parentNamespace && $this->hasModuleId($parentNamespace)) {
            $parent = $this->getByModuleId($parentNamespace);
            $insertPosition = $this->findChildInsertPosition($parent);
            array_splice($this->chain, $insertPosition, 0, [$templateType]);
        } else {
            $this->chain[] = $templateType;
        }
    }

    public function remove(TemplateTypeInterface $templateType): void
    {
        $fullyQualifiedName = $templateType->getFullyQualifiedName();

        foreach ($this->chain as $index => $item) {
            if ($item->getFullyQualifiedName() === $fullyQualifiedName) {
                array_splice($this->chain, $index, 1);
                break;
            }
        }
    }

    public function appendChain(TemplateChain $chain): void
    {
        foreach ($chain as $templateType) {
            $this->append($templateType);
        }
    }

    public function hasModuleId(string $moduleId): bool
    {
        foreach ($this->chain as $templateType) {
            if ($templateType->getNamespace() === $moduleId) {
                return true;
            }
        }

        return false;
    }

    public function getByModuleId(string $moduleId): TemplateTypeInterface
    {
        foreach ($this->chain as $templateType) {
            if ($templateType->getNamespace() === $moduleId) {
                return $templateType;
            }
        }
    }

    public function has(TemplateTypeInterface $templateType): bool
    {
        $fullyQualifiedName = $templateType->getFullyQualifiedName();

        foreach ($this->chain as $item) {
            if ($item->getFullyQualifiedName() === $fullyQualifiedName) {
                return true;
            }
        }

        return false;
    }

    public function getParent(TemplateTypeInterface $templateType): TemplateTypeInterface
    {
        $fullyQualifiedName = $templateType->getFullyQualifiedName();

        foreach ($this->chain as $index => $item) {
            if ($item->getFullyQualifiedName() === $fullyQualifiedName) {
                return $this->chain[$index + 1];
            }
        }
    }

    public function getLastChild(): TemplateTypeInterface
    {
        reset($this->chain);
        return current($this->chain);
    }

    public function hasParent(TemplateTypeInterface $templateType): bool
    {
        $lastItem = end($this->chain);
        reset($this->chain);

        return $templateType->getFullyQualifiedName() !== $lastItem->getFullyQualifiedName();
    }

    public function count(): int
    {
        return count($this->chain);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->chain);
    }

    private function findChildInsertPosition(TemplateTypeInterface $parent): int
    {
        $parentFullyQualifiedName = $parent->getFullyQualifiedName();

        foreach ($this->chain as $index => $entry) {
            if ($entry->getFullyQualifiedName() === $parentFullyQualifiedName) {
                return $index;
            }
        }

        return 0;
    }
}
