<?php
defined('TYPO3') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function(string $extensionKey) {
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        "@import 'EXT:".$extensionKey."/Configuration/TypoScript/setup.typoscript'",
    );
}, 'hh_ext_filesize');
