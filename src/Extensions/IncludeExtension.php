<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeDynamicLogic;
use OxidEsales\Twig\TokenParser\IncludeDynamicTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

/**
 * Class IncludeExtension
 */
class IncludeExtension extends AbstractExtension
{
    /**
     * @var IncludeDynamicLogic
     */
    private $includeDynamicLogic;

    /**
     * IncludeExtension constructor.
     *
     * @param IncludeDynamicLogic $includeDynamicLogic
     */
    public function __construct(IncludeDynamicLogic $includeDynamicLogic)
    {
        $this->includeDynamicLogic = $includeDynamicLogic;
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new IncludeDynamicTokenParser()];
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderForCache(array $parameters): string
    {
        return $this->includeDynamicLogic->renderForCache($parameters);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function includeDynamicPrefix(array $parameters): array
    {
        return $this->includeDynamicLogic->includeDynamicPrefix($parameters);
    }
}
