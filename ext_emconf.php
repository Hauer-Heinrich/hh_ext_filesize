<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "extension_skeleton"
 *
 * Auto generated by Extension Builder 2018-05-08
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['hh_ext_filesize'] = [
    'title' => 'Hauer-Heinrich - Filesize module',
    'description' => 'List all file-sizes within given directory which are greater then e. g. 300kb.',
    'category' => 'module',
    'author' => 'Christian Hackl',
    'author_email' => 'hackl.chris@googlemail.com',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'php' => '>=8',
            'typo3' => '^12.4.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhExtFilesize\\' => 'Classes'
        ],
    ],
];
