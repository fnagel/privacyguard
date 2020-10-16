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
    'description' => 'This scheduler task cleans database records in TYPO3 CMS to improve the privacy of your customers. Currently works with formhandler, ve_guestbook, femanager, mkphpids, spamshield and sys_log table. Useful for GDPR / DSGVO compliance.',
    'category' => 'services',
    'author' => 'Felix Nagel',
    'author_email' => 'info@felixnagel.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 0,
    'version' => '3.1.2-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.0.0-7.4.99',
            'typo3' => '9.4.0-10.4.99',
            'scheduler' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
