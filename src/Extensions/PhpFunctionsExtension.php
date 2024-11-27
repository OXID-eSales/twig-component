<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @deprecated
 */
class PhpFunctionsExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'count',
                'count',
                [
                    'deprecated' => true,
                    'alternative' => 'length',
                ]
            ),
            new TwigFunction(
                'empty',
                [
                    $this,
                    'emptyWrapper',
                    [
                        'deprecated' => true,
                        'alternative' => 'length',
                    ],
                ]
            ),
            new TwigFunction(
                'isset',
                [
                    $this,
                    'twigIsset',
                    [
                        'deprecated' => true,
                        'alternative' => 'is defined',
                    ],
                ]
            )
        ];
    }

    /**
     * isset wrapper, isset is a language construct and can't be called as a function in callback
     * @link https://www.php.net/manual/en/functions.variable-functions.php
     */
    public function twigIsset($value = null): bool
    {
        return isset($value);
    }

    /**
     * empty is a language construct and can't be called as a function callback
     * @link https://www.php.net/manual/en/functions.variable-functions.php
     */
    public function emptyWrapper(mixed $value = null): bool
    {
        return empty($value);
    }
}
