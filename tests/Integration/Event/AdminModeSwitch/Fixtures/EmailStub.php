<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Event\AdminModeSwitch\Fixtures;

use OxidEsales\Eshop\Core\Email;

final class EmailStub extends Email
{
    public function send(): void
    {
    }
}
