<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use Exception;

class TemplateLoaderNameParser
{
    /**
     * Checks whether string is valid template name
     *
     * @param string $name
     *
     * @return bool
     */
    public function isValidName(string $name): bool
    {
        return count(explode('::', $name)) === 3;
    }

    /**
     * loaderName::key::value?param1=value1&param2=value2
     *
     * @param string $name
     *
     * @return string
     */
    public function getLoaderName(string $name): string
    {
        return $this->getNamePart($name, 0);
    }

    /**
     * loaderName::key::value?param1=value1&param2=value2
     *
     * @param string $name
     *
     * @return string
     */
    public function getKey(string $name): string
    {
        return $this->getNamePart($name, 1);
    }

    /**
     * loaderName::key::value?param1=value1&param2=value2
     *
     * @param string $name
     *
     * @return string
     */
    public function getValue(string $name): string
    {
        return $this->getNamePart($name, 2);
    }

    /**
     * loaderName::key::value?param1=value1&param2=value2
     *
     * @param string $name
     *
     * @return array
     */
    public function getParameters(string $name): array
    {
        $parameters = [];

        if (count(explode('?', $name)) >= 2) {
            $parametersString = explode('?', $name)[1];

            parse_str($parametersString, $parameters);
        }

        return $parameters;
    }

    /**
     * loaderName::key::value?param1=value1&param2=value2
     *     [0]     [1]   [2]
     *
     * @param string $name
     * @param int    $index
     *
     * @return string
     */
    private function getNamePart(string $name, int $index): string
    {
        if (!$this->isValidName($name)) {
            throw new Exception("Invalid template name.");
        }

        $nameParts = explode('?', $name)[0];

        return explode('::', $nameParts)[$index];
    }
}
