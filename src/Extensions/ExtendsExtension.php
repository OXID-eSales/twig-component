<?php

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\Twig\TokenParser\ExtendsChainTokenParser;
use Twig\Extension\AbstractExtension;

class ExtendsExtension extends AbstractExtension
{
    public function getTokenParsers(): array
    {
        return [
            new ExtendsChainTokenParser(),
        ];
    }
}
