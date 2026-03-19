<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Node;

use Twig\Node\Node;
use Twig\Compiler;

class HasRightsNode extends Node
{
    /**
     * @deprecated $tag will be removed in next major version, the tag is now automatically set by the Parser.
     */
    public function __construct(Node $body, Node $parameters, int $lineno, ?string $tag = null)
    {
        parent::__construct(['body' => $body, 'parameters' => $parameters], [], $lineno);
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler->subcompile($this->getNode('body'));
    }
}
