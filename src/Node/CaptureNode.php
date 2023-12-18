<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

class CaptureNode extends Node
{
    private string $captureNameAttribute = 'name';

    private string $captureAssignAttribute = 'assign';

    private string $captureAppendAttribute = 'append';

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var string
     */
    private $variableName;

    public function __construct(string $attributeName, string $variableName, Node $body, int $line, string $tag = null)
    {
        parent::__construct(
            ['body' => $body],
            ['attributeName' => $attributeName, 'variableName' => $variableName],
            $line,
            $tag
        );
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $this->attributeName = $this->getAttribute('attributeName');
        $this->variableName = $this->getAttribute('variableName');

        $this->compileCaptureCode($compiler);
    }

    /**
     * @param Compiler $compiler
     */
    private function compileCaptureCode(Compiler $compiler)
    {
        $this->setCaptureContentToVariable($compiler);
        $this->compileCodeForGivenAttribute($compiler);
        $this->unsetCaptureContent($compiler);
    }

    /**
     * @param Compiler $compiler
     */
    private function setCaptureContentToVariable(Compiler $compiler)
    {
        $compiler
            ->write("ob_start();\n");

        $compiler->subcompile($this->getNode("body"));

        $compiler
            ->write("\$captureContent = ob_get_clean();\n");
    }

    /**
     * @param Compiler $compiler
     */
    private function compileCodeForGivenAttribute(Compiler $compiler)
    {
        if ($this->attributeName == $this->captureNameAttribute) {
            $this->compileCaptureName($compiler);
        } elseif ($this->attributeName == $this->captureAssignAttribute) {
            $this->compileCaptureAssign($compiler);
        } elseif ($this->attributeName == $this->captureAppendAttribute) {
            $this->compileCaptureAppend($compiler);
        }
    }

    /**
     * @param Compiler $compiler
     */
    private function compileCaptureName(Compiler $compiler)
    {
        $compiler
            ->write("\$context['twig']['capture']['" . $this->variableName . "'] = \$captureContent;\n");
    }

    /**
     * @param Compiler $compiler
     */
    private function compileCaptureAssign(Compiler $compiler)
    {
        $compiler
            ->write("if ('" . $this->variableName . "' != '') {\n")
            ->write("\$context['" . $this->variableName . "'] = \$captureContent;\n")
            ->write(("}\n"));
    }

    /**
     * @param Compiler $compiler
     */
    private function compileCaptureAppend(Compiler $compiler)
    {
        $compiler
            ->write("if ('" . $this->variableName . "' != '' && isset(\$captureContent)) {\n")
            ->write("if (!isset(\$context['" . $this->variableName . "'])) {\n")
            ->write("\$context['" . $this->variableName . "'] = [];\n")
            ->write(("}\n"))
            ->write("if (!is_array(\$context['" . $this->variableName . "'])) {\n")
            ->write("\$context['" . $this->variableName . "'] = [\$context['" . $this->variableName . "']];\n")
            ->write(("}\n"))
            ->write("\$context['" . $this->variableName . "'][] = \$captureContent;\n")
            ->write(("}\n"));
    }

    /**
     * @param Compiler $compiler
     */
    private function unsetCaptureContent(Compiler $compiler)
    {
        $compiler
            ->write("unset(\$captureContent);\n");
    }
}
