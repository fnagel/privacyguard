<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_privacyguard_cleaner'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'LLL:EXT:' . $_EXTKEY . '/lang/locallang.xml:localconf_title',
    'description'      => 'LLL:EXT:' . $_EXTKEY . '/lang/locallang.xml:localconf_description',
	'additionalFields' => 'tx_privacyguard_cleaner_addFields'
);
?>