<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Module1\Controller\ModuleController;
use OxidEsales\Module1\Controller\ModuleControllerMissingTemplate;

$sMetadataVersion = '2.1';

$aModule = [
    'id' => 'module1',
    'controllers' => [
        'module1_controller' => ModuleController::class,
        'module1_controller_missing_template' => ModuleControllerMissingTemplate::class,
    ],
];
