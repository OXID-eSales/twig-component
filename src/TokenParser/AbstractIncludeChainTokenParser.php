<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\TokenParser;

use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\TokenParser\IncludeTokenParser;

abstract class AbstractIncludeChainTokenParser extends IncludeTokenParser
{
    protected function createRuntimeResolutionExpression(string $templateName, int $line): FunctionExpression
    {
        $twigFunction = $this->parser->getEnvironment()->getFunction('oxid_resolve_last_child');
        return new FunctionExpression(
            $twigFunction,
            new Node([new ConstantExpression($templateName, $line)]),
            $line
        );
    }
}
