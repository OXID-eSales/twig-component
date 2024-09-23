<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\Template;

abstract class AbstractExtensionTestCase extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    protected AbstractExtension $extension;

    public function setUp(): void
    {
        parent::setUp();

        $this->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction();

        parent::tearDown();
    }

    protected function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }

    public function setLanguage(int $languageId): void
    {
        $oxLang = Registry::getLang();
        $oxLang->setBaseLanguage($languageId);
        $oxLang->setTplLanguage($languageId);
    }

    public function setAdminMode(bool $adminMode): void
    {
        Registry::getConfig()->setAdminMode($adminMode);
    }
}
