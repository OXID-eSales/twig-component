<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Node;

use OxidEsales\Twig\Extensions\IfContentExtension;
use Twig\Node\Node;
use Twig\Compiler;

class IfContentNode extends Node
{
    /**
     * @deprecated $tag will be removed in next major version, the tag is now automatically set by the Parser.
     */
    public function __construct(Node $body, array $reference, Node $variable, int $lineno, ?string $tag = null)
    {
        $nodes = [
                     'body' => $body,
                     'variable' => $variable
                 ] + $reference;

        parent::__construct($nodes, [], $lineno);
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->subcompile($this->getNode('variable'), false)
            ->raw(" = ")
            ->raw("\$this->extensions['" . IfContentExtension::class . "']->getContent(");

        if ($this->hasNode('ident')) {
            $compiler->subcompile($this->getNode('ident'))->raw(', null');
        } elseif ($this->hasNode('oxid')) {
            $compiler->raw('null, ')->subcompile($this->getNode('oxid'));
        }

        $compiler->raw(");\n");

        $compiler
            ->write("if(")
            ->subcompile($this->getNode('variable'), false)
            ->write(") { \n")
            ->subcompile($this->getNode('body'))
            ->write(" } \n")
            ->write("unset(")->subcompile($this->getNode('variable'))->raw(");\n");
    }
}
