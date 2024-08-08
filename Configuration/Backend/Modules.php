<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use HauerHeinrich\HhExtFilesize\Controller\Backend\FileSizeModuleController;

/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'web_examples' => [
        'parent' => 'file',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/files/filesizes',
        'labels' => 'LLL:EXT:examples/Resources/Private/Language/Module/locallang_mod.xlf',
        'extensionName' => 'Examples',
        'iconIdentifier' => 'tx_examples-backend-module',
        'controllerActions' => [
            FileSizeModuleController::class => [
                'fileSizes',
            ],
        ],
    ],
];
