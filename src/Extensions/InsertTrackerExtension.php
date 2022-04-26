<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class InsertTrackerExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [new TwigFunction('insert_tracker', [$this, 'insertTracker'], ['needs_environment' => true])];
    }

    /**
     * @param Environment|null $env
     * @param array $params
     *
     * @return string
     */
    public function insertTracker(Environment $env = null, $params = []): string
    {
        return '';
    }
}
