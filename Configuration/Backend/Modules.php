<?php

use \HauerHeinrich\HhExtFilesize\Controller\Backend\FileSizeModuleController;

return [
    'file_HhExtFilesizeTxHhextfilesize' => [
        'parent' => 'file',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/files/filesizes',
        'labels' => 'LLL:EXT:hh_ext_filesize/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'HhExtFilesize',
        'iconIdentifier' => 'module-filesize',
        'controllerActions' => [
            FileSizeModuleController::class => [
                'fileSizes',
            ],
        ],
    ],
];
