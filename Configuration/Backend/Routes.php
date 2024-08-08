<?php

return [
    'file_HhExtFilesizeTxHhextfilesize' => [
        'path' => '/module/files/filesizes',
        'access' => 'public',
        'target' => HauerHeinrich\HhExtFilesize\Controller\Backend\FileSizeModuleController::class . '::fileSizesAction',
    ],
];
