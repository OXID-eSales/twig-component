<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Node;

use OxidEsales\Twig\Extensions\IfContentExtension;
use Twig\Node\Node;
use Twig\Compiler;

/**
 * Class IfContentNode
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IfContentNode extends Node
{
    /**
     * IfContentNode constructor.
     *
     * @param Node   $body
     * @param array  $reference
     * @param Node   $variable
     * @param int    $lineno
     * @param string $tag
     */
    public function __construct(Node $body, array $reference, Node $variable, int $lineno, string $tag = 'ifcontent')
    {
        $nodes = [
                     'body' => $body,
                     'variable' => $variable
                 ] + $reference;

        parent::__construct($nodes, [], $lineno, $tag);
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
            ->subcompile($this->getNode('body'))
            ->write("unset(")->subcompile($this->getNode('variable'))->raw(");\n");;
    }
}
