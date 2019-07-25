<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Node;

use Twig\Node\Node;
use Twig\Compiler;

/**
 * Class HasRightsNode
 *
 * @package OxidEsales\Twig\Node
 */
class HasRightsNode extends Node
{
    /**
     * HasRightsNode constructor.
     *
     * @param Node   $body
     * @param Node   $parameters
     * @param int    $lineno
     * @param string $tag
     */
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
