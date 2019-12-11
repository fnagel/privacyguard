<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['FelixNagel\\PrivacyGuard\\Task\\PrivacyGuardTask'] = [
    'extension' => 'privacyguard',
    'title' => 'LLL:EXT:privacyguard/Resources/Private/Language/locallang.xml:localconf_title',
    'description' => 'LLL:EXT:privacyguard/Resources/Private/Language/locallang.xml:localconf_description',
    'additionalFields' => 'FelixNagel\\PrivacyGuard\\Task\\PrivacyGuardAdditionalFieldProvider',
];
