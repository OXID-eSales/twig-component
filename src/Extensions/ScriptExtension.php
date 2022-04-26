<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ScriptLogic;
use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptExtension extends AbstractExtension
{
    public function __construct(private ScriptLogic $scriptLogic)
    {
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [new TwigFunction('script', [$this, 'script'], ['needs_context' => true, 'is_safe' => ['html']])];
    }

    /**
     * @param array $context
     * @param array $parameters
     *
     * @return string
     * @throws Error
     */
    public function script(array $context = [], array $parameters = []): string
    {
        $isDynamic = isset($context['__oxid_include_dynamic']) && (bool) $context['__oxid_include_dynamic'];
        $priority = !empty($parameters['priority']) ? $parameters['priority'] : 3;
        $widget = !empty($parameters['widget']) ? $parameters['widget'] : '';
        $isInWidget = !empty($parameters['inWidget']) ? $parameters['inWidget'] : false;
        $output = '';

        if (isset($parameters['add'])) {
            if (empty($parameters['add'])) {
                throw new Error("{{ script }} parameter 'add' can not be empty!");
            }

            $this->scriptLogic->add($parameters['add'], $isDynamic);
        } elseif (isset($parameters['include'])) {
            if (empty($parameters['include'])) {
                throw new Error("{{ script }} parameter 'include' can not be empty!");
            }

            $this->scriptLogic->include($parameters['include'], $priority, $isDynamic);
        } else {
            $output = $this->scriptLogic->render($widget, $isInWidget, $isDynamic);
        }

        return $output;
    }
}
