<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Node;

use OxidEsales\Twig\Extensions\IncludeExtension;
use Twig\Node\IncludeNode;
use Twig\Compiler;

class IncludeDynamicNode extends IncludeNode
{
    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $this->preCompile($compiler);

        if ($this->hasNode('variables')) {
            $compiler->write("\$parameters = ");
            $compiler->subcompile($this->getNode('variables'));
            $compiler->raw(";\n");
        }

        $compiler->write("if (!empty(\$context[\"_render4cache\"])) {\n");
        $compiler->indent();
        $this->ifRender4Cache($compiler);
        $compiler->outdent();
        $compiler->write("} else {\n");
        $compiler->indent();
        $this->ifNotRender4Cache($compiler);
        $compiler->outdent();
        $compiler->write("}\n");

        $this->postCompile($compiler);
    }

    /**
     * @param Compiler $compiler
     */
    private function preCompile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->write("try {\n")
                ->indent();
        }
    }

    /**
     * @param Compiler $compiler
     */
    private function ifRender4Cache(Compiler $compiler)
    {
        $compiler->write("echo \$this");
        $compiler->raw("->extensions['" . IncludeExtension::class . "']");
        if ($this->hasNode('variables')) {
            $compiler->raw("->renderForCache(array_merge(\$parameters, ['file' => ");
            $compiler->subcompile($this->getNode('expr'));
            $compiler->raw("]));\n");
        } else {
            $compiler->raw("->renderForCache(['file' => ");
            $compiler->subcompile($this->getNode('expr'));
            $compiler->raw("]);\n");
        }
    }

    /**
     * @param Compiler $compiler
     */
    private function ifNotRender4Cache(Compiler $compiler)
    {
        if ($this->hasNode('variables')) {
            $compiler->write("\$parameters = \$this");
            $compiler->raw("->extensions['" . IncludeExtension::class . "']");
            $compiler->raw("->includeDynamicPrefix(\$parameters);\n");
        }

        $this->includeTemplate($compiler);
    }

    /**
     * @param Compiler $compiler
     */
    private function includeTemplate(Compiler $compiler)
    {
        $this->addGetTemplate($compiler);

        $compiler->raw('->display(');

        $this->addTemplateArguments($compiler);

        $compiler->raw(");\n");
    }

    /**
     * @param Compiler $compiler
     */
    protected function addTemplateArguments(Compiler $compiler)
    {
        if (!$this->hasNode('variables')) {
            $compiler->raw(false === $this->getAttribute('only') ? '$context' : '[]');
        } elseif (false === $this->getAttribute('only')) {
            $compiler->raw('array_merge($context, $parameters)');
        } else {
            $compiler->raw('$parameters');
        }
    }

    /**
     * @param Compiler $compiler
     */
    private function postCompile(Compiler $compiler)
    {
        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->outdent()
                ->write("} catch (\Twig\Error\LoaderError \$e) {\n")
                ->indent()
                ->write("// ignore missing template\n")
                ->outdent()
                ->write("}\n\n");
        }
    }
}
