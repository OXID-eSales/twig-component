<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Eshop\Core\Registry;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;

abstract class AbstractExtensionTest extends \PHPUnit\Framework\TestCase
{
    /** @var AbstractExtension */
    protected $extension;

    /**
     * @param string $template
     *
     * @return \Twig_Template
     */
    protected function getTemplate(string $template): \Twig_Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate('index');
    }

    /**
     * Sets language
     *
     * @param int $languageId
     */
    public function setLanguage($languageId)
    {
        $oxLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $oxLang->setBaseLanguage($languageId);
        $oxLang->setTplLanguage($languageId);
    }

    /**
     * Sets OXID shop admin mode.
     *
     * @param bool $adminMode set to admin mode TRUE / FALSE.
     */
    public function setAdminMode($adminMode)
    {
        Registry::getConfig()->setAdminMode($adminMode);
    }
}
