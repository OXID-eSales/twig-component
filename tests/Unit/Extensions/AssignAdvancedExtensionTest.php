<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\AssignAdvancedLogic;
use OxidEsales\Twig\Extensions\AssignAdvancedExtension;
use \PHPUnit\Framework\TestCase;

class AssignAdvancedExtensionTest extends TestCase
{
    /**
     * @var AssignAdvancedExtension
     */
    private $assignAdvancedExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $assignAdvancedLogic = new AssignAdvancedLogic();
        $this->assignAdvancedExtension = new AssignAdvancedExtension($assignAdvancedLogic);
    }

    public function testAssignAdvanced(): void
    {
        $a = $this->assignAdvancedExtension->assignAdvanced('foo');
        $this->assertEquals('foo', $a);
    }
}
