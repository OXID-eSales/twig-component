<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\Template;

abstract class AbstractExtensionTestCase extends TestCase
{
    protected AbstractExtension $extension;

    protected function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }

    /**
     * Sets language
     *
     * @param int $languageId
     */
    public function setLanguage($languageId): void
    {
        $oxLang = Registry::getLang();
        $oxLang->setBaseLanguage($languageId);
        $oxLang->setTplLanguage($languageId);
    }

    /**
     * Sets OXID shop admin mode.
     *
     * @param bool $adminMode set to admin mode TRUE / FALSE.
     */
    public function setAdminMode($adminMode): void
    {
        Registry::getConfig()->setAdminMode($adminMode);
    }
}
