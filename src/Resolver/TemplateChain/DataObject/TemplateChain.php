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
        $index = $this->findIndexByFullyQualifiedName(
            $templateType->getFullyQualifiedName()
        );

        if ($index !== null) {
            array_splice($this->chain, $index, 1);
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
        return $this->findIndexByModuleId($moduleId) !== null;
    }

    public function getByModuleId(string $moduleId): ?TemplateTypeInterface
    {
        return $this->findByModuleIdInternal($moduleId);
    }

    public function has(TemplateTypeInterface $templateType): bool
    {
        return $this->findIndexByFullyQualifiedName(
            $templateType->getFullyQualifiedName()
        ) !== null;
    }

    public function getParent(TemplateTypeInterface $templateType): ?TemplateTypeInterface
    {
        $index = $this->findIndexByFullyQualifiedName($templateType->getFullyQualifiedName());

        return $index !== null && isset($this->chain[$index + 1])
            ? $this->chain[$index + 1]
            : null;
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
        return $this->findIndexByFullyQualifiedName($parent->getFullyQualifiedName()) ?? 0;
    }

    private function findIndexByFullyQualifiedName(string $fullyQualifiedName): ?int
    {
        return array_find_key($this->chain, static fn($item) => $item->getFullyQualifiedName() === $fullyQualifiedName);
    }

    private function findIndexByModuleId(string $moduleId): ?int
    {
        return array_find_key($this->chain, static fn($item) => $item->getNamespace() === $moduleId);
    }

    private function findByModuleIdInternal(string $moduleId): ?TemplateTypeInterface
    {
        $index = $this->findIndexByModuleId($moduleId);
        return $index !== null ? $this->chain[$index] : null;
    }
}
