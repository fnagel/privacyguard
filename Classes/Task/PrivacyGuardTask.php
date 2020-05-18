<?php

namespace FelixNagel\PrivacyGuard\Task;

/**
 * This file is part of the "privacyguard" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class PrivacyGuardTask.
 */
class PrivacyGuardTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
    /**
     * @var bool
     */
    protected $debugging = false;

    /**
     * Array of supported extensions
     *
     * @var string
     */
    public static $supportedExtensions = [
        'formhandler' => 'Formhandler (EXT:formhandler)',
        'femanager' => 'FE Manager (EXT:femanager - BETA!)',
        'mkphpids' => 'PHPIDS (EXT:mkphpids)',
        've_guestbook' => 'Modern Guestbook (EXT:ve_guestbook)',
        'sys_log' => 'TYPO3 sys log',
        'spamshield' => 'spamshield (EXT:spamshield)',
    ];

    /**
     * @var string
     */
    public $privacyguard_extkey;

    /**
     * @var string
     */
    public $privacyguard_time;

    /**
     * @var string
     */
    public $privacyguard_method;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function execute()
    {
        $this->cleanValues();

        if ($this->privacyguard_extkey !== 'sys_log' && !$this->isExtensionLoaded()) {
            $this->log('Extension ' . $this->privacyguard_extkey . ' is not installed');

            return false;
        }

        return $this->chooseExtension();
    }

    /**
     * Init cleaning.
     *
     * @todo Make this more fancy and extensible (Hooks)
     *
     * @return bool
     */
    protected function chooseExtension()
    {
        $flag = false;

        switch ($this->privacyguard_extkey) {
            case 'formhandler':
                $flag = $this->extFormhandler();
                break;

            case 'femanager':
                $flag = $this->extFeManager();
                break;

            case 'mkphpids':
                $flag = $this->extMkPhpids();
                break;

            case 've_guestbook':
                $flag = $this->extVeGuestbook();
                break;

            case 'sys_log':
                $flag = $this->extSysLog();
                break;

            case 'spamshield':
                $flag = $this->extSpamshield();
                break;

            default:
        }

        return $flag;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        $text = '';

        $text .= 'EXT:'.$this->privacyguard_extkey;
        $text .= ', '.$this->translate('addfields_method_'.$this->privacyguard_method);
        $text .= ', '.$this->privacyguard_time;

        return $text;
    }

    /**
     */
    protected function cleanValues()
    {
        $this->privacyguard_time = strip_tags($this->privacyguard_time);
        $this->privacyguard_extkey = strip_tags($this->privacyguard_extkey);
        $this->privacyguard_method = strip_tags($this->privacyguard_method);

        $this->log('$this->time: '.$this->privacyguard_time);
        $this->log('$this->extkey: '.$this->privacyguard_extkey);
        $this->log('$this->method: '.$this->privacyguard_method);
    }

    /**
     * @param $table
     * @param $fields
     *
     * @return bool
     */
    protected function processCleaning($table, $fields)
    {
        $flag = false;

        if (strlen($table) < 3) {
            $this->log('No TABLE given');

            return false;
        }


        try {
            $queryBuilder = $this->getQueryBuilder($table);

            switch ($this->privacyguard_method) {
                case 'delete_ip':
                case 'anonymize_ip':
                    $where = $this->getWhereConstraints($queryBuilder, $table);
                    $queryBuilder
                        ->update($table)
                        ->where($where);

                    foreach ($fields as $property => $value) {
                        $queryBuilder->set($property, $value);
                    }

                    $queryBuilder->execute();

                    if ($this->debugging) {
                        $this->log('SQL DEBUG: '.$queryBuilder->getSQL());
                    } else {
                        $flag = true;
                    }
                    break;

                case 'delete_all':
                    if ($this->privacyguard_time) {
                        $where = $this->getWhereConstraints($queryBuilder, $table);
                        $queryBuilder
                            ->delete($table)
                            ->where($where)
                            ->execute();
                        if ($this->debugging) {
                            $this->log('SQL DEBUG: '.$queryBuilder->getSQL());
                        } else {
                            $flag = true;
                        }
                    } else {
                        // Use truncate for better performance when all entries should be deleted
                        $queryBuilder->getConnection()->truncate($table, false);
                        if ($this->debugging) {
                            $this->log('SQL DEBUG: TRUNCATE TABLE '.$table.';');
                        } else {
                            $flag = true;
                        }
                    }
                    break;

                default:
                    return false;
            }
        } catch (\Exception $exception) {
            if (!$this->debugging) {
                throw new \Exception(
                    'tx_privacyguard_cleaner failed for table '.$table.' with error: '.$exception->getMessage(),
                    1308255491
                );
            }
        }

        return $flag;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $table
     *
     * @return string
     */
    protected function getWhereConstraints(QueryBuilder $queryBuilder, $table)
    {
        $where = '';
        $timestamp = $this->getWhereTimestamp();

        if ($this->privacyguard_time) {
            switch ($table) {
                case 'tx_mkphpids_log':
                    $where = $queryBuilder->expr()->lt(
                        'UNIX_TIMESTAMP(created)',
                        $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT)
                    );
                    break;

                case 'sys_log':
                    $where = $queryBuilder->expr()->lt(
                        'tstamp',
                        $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT)
                    );
                    break;

                default:
                    $where = $queryBuilder->expr()->lt(
                        'crdate',
                        $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT)
                    );
            }
        }

        return $where;
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    protected function getQueryBuilder($table)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        return $objectManager->get(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

    /**
     * @return int|string
     */
    protected function getWhereTimestamp()
    {
        $timestamp = '';

        // choose correct time
        switch ($this->privacyguard_time) {
            case '24h':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('Y'));
                break;

            case '48h':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 2, date('Y'));
                break;

            case '72h':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 3, date('Y'));
                break;

            case '7d':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 7, date('Y'));
                break;

            case '14d':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 14, date('Y'));
                break;

            case '1m':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m') - 1, date('d'), date('Y'));
                break;

            case '3m':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m') - 3, date('d'), date('Y'));
                break;

            case '6m':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m') - 6, date('d'), date('Y'));
                break;

            case '12m':
                $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y') - 1);
                break;

            default:
        }

        return $timestamp;
    }

    /**
     * @return bool
     */
    public function extFormhandler()
    {
        $fields = [];

        $table = 'tx_formhandler_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extFeManager()
    {
        $fields = [];

        $table = 'tx_femanager_domain_model_log';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extMkPhpids()
    {
        $fields = [];

        $table = 'tx_mkphpids_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extVeGuestbook()
    {
        $fields = [];

        $table = 'tx_veguestbook_entries';
        $fields['remote_addr'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extSysLog()
    {
        $fields = [];

        $table = 'sys_log';
        $fields['IP'] = '';
        $fields['log_data'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extSpamshield()
    {
        $fields = [];

        $table = 'tx_spamshield_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * Translate by key.
     *
     * @param string $key
     * @param string $prefix
     *
     * @return string
     */
    protected function translate($key, $prefix = 'LLL:EXT:privacyguard/Resources/Private/Language/locallang.xml:')
    {
        return $GLOBALS['LANG']->sL($prefix.$key);
    }

    /**
     * @return bool
     */
    protected function isExtensionLoaded()
    {
        return ExtensionManagementUtility::isLoaded($this->privacyguard_extkey);
    }

    /**
     * @param $msg
     */
    protected function log($msg)
    {
        if ($this->debugging) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($msg);
        }
    }
}
