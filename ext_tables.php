<?php
defined('TYPO3') || die('Access denied.');

call_user_func(function(string $extensionKey) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        $extensionKey,
        'file',
        'tx_hhextfilesize',
        'bottom',
        [
            \HauerHeinrich\HhExtFilesize\Controller\Backend\FileSizeModuleController::class => 'fileSizes,',
        ],
        [
            'access' => 'user,group',
            'iconIdentifier' => 'module-beuser',
            'labels' => 'LLL:EXT:hh_ext_filesize/Resources/Private/Language/locallang_mod.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/Tree/FileStorageTreeContainer',
            'inheritNavigationComponentFromMainModule' => false,
        ]
    );
}, 'hh_ext_filesize');
