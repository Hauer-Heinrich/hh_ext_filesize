<?php

return [
    'file_HhExtFilesizeTxHhextfilesize' => [
        'path' => '/file-sizes',
        'access' => 'public',
        'target' => HauerHeinrich\HhExtFilesize\Controller\Backend\FileSizeModuleController::class . '::fileSizesAction',
    ],
];
