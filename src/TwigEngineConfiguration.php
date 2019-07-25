<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

/**
 * Class TwigEngineConfiguration
 *
 * @package OxidEsales\Twig
 */
class TwigEngineConfiguration implements TwigEngineConfigurationInterface
{
    /**
     * @var TwigContextInterface
     */
    private $context;

    /**
     * TemplateEngineConfiguration constructor.
     *
     * @param TwigContextInterface $context
     */
    public function __construct(TwigContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Return an array of twig parameters to configure.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'debug' => $this->context->getIsDebug(),
            'cache' => $this->context->getCacheDir(),
        ];
    }
}
