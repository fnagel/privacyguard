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

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Privacy Guard',
	'description' => 'This scheduler task cleans TYPO3 to improve the privacy of your customers. Currently works with comments, formhandler, px_phpids, sfpantispam and ve_guestbook, spamshield and sys_log table.',
	'category' => 'services',
	'author' => 'Felix Nagel',
	'author_email' => 'info@felixnagel.com',
	'shy' => '',
	'dependencies' => 'scheduler',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.4.0',
	'constraints' => array(
		'depends' => array(
			'scheduler' => '',
			'typo3' => '4.3.0-4.7.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"904d";s:16:"ext_autoload.php";s:4:"5bd6";s:12:"ext_icon.gif";s:4:"45cd";s:17:"ext_localconf.php";s:4:"499d";s:14:"doc/manual.sxw";s:4:"0ae3";s:18:"lang/locallang.xml";s:4:"1846";s:39:"tasks/class.tx_privacyguard_cleaner.php";s:4:"fae3";s:49:"tasks/class.tx_privacyguard_cleaner_addFields.php";s:4:"5372";}',
	'suggests' => array(),
);

?>