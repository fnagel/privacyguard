<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "privacyguard".
 *
 * Auto generated 03-06-2013 20:42
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Privacy Guard',
    'description' => 'This scheduler task cleans TYPO3 to improve the privacy of your customers. Currently works with formhandler, ve_guestbook, femanager, mkphpids, spamshield and sys_log table. Useful for GDPR / DSGVO compliance.',
    'category' => 'services',
    'author' => 'Felix Nagel',
    'author_email' => 'info@felixnagel.com',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'php' => '7.0.0-7.2.99',
            'typo3' => '8.7.0-9.5.99',
            'scheduler' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'suggests' => [],
];
