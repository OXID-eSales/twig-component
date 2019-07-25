<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenParser\TokenParserInterface;

/**
 * Class HasRightsExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class HasRightsExtension extends AbstractExtension
{
    /**
     * @var AbstractTokenParser
     */
    protected $hasRightsTokenParser;

    /**
     * HasRightsExtension constructor.
     *
     * @param AbstractTokenParser $hasRightsTokenParser
     */
    public function __construct(AbstractTokenParser $hasRightsTokenParser)
    {
        $this->hasRightsTokenParser = $hasRightsTokenParser;
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [$this->hasRightsTokenParser];
    }
}
