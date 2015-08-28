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
	'version' => '2.0.0-dev',
	'constraints' => array(
		'depends' => array(
			'scheduler' => '',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'suggests' => array(),
);

?>