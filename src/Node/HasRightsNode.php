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
    public function __construct(Node $body, Node $parameters, int $lineno, $tag = 'hasrights')
    {
        parent::__construct(['body' => $body, 'parameters' => $parameters], [], $lineno, $tag);
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
