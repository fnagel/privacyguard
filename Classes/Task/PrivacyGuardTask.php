<?php

namespace TYPO3\PrivacyGuard\Task;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
    public static $supportedExtensions = array(
        'formhandler' => 'Formhandler (EXT:formhandler)',
        'px_phpids' => 'PHPIDS (EXT:px_phpids)',
        'sys_log' => 'TYPO3 sys log',
        'spamshield' => 'spamshield (EXT:spamshield)',
    );

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
     * @var \TYPO3\CMS\Lang\LanguageService
     */
    protected $languageService;

    /**
     * Construct class.
     */
    public function __construct()
    {
        parent::__construct();

        $this->languageService = $GLOBALS['LANG'];
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function execute()
    {
        $this->cleanValues();

        if ($this->privacyguard_extkey !== 'sys_log' && !$this->isExtensionLoaded()) {
            $this->log('Extension ' . $this->privacyguard_extkey . ' is not installed', 3);

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

            case 'px_phpids':
                $flag = $this->extPxPhpids();
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
     *
     * @throws \Exception
     */
    protected function processCleaning($table, $fields)
    {
        $flag = false;

        if (strlen($table) < 3) {
            $this->log('No TABLE given', 3);

            return false;
        }

        switch ($this->privacyguard_method) {
            case 'delete_ip':
            case 'anonymize_ip':
                $where = $this->getWhereClause($table);
                if ($this->debugging) {
                    $this->log('SQL DEBUG: '.$this->getDatabase()->UPDATEquery($table, $where, $fields));
                } else {
                    $res = $this->getDatabase()->exec_UPDATEquery($table, $where, $fields);
                    $flag = true;
                }
                break;

            case 'delete_all':
                if ($this->privacyguard_time) {
                    $where = $this->getWhereClause($table);
                    if ($this->debugging) {
                        $this->log('SQL DEBUG: '.$this->getDatabase()->DELETEquery($table, $where));
                    } else {
                        $res = $this->getDatabase()->exec_DELETEquery($table, $where);
                        $flag = true;
                    }
                } else {
                    // use truncate for better performance when all entries should be deleted
                    if ($this->debugging) {
                        $this->log('SQL DEBUG: TRUNCATE TABLE '.$table.';');
                    } else {
                        $res = $this->getDatabase()->sql_query('TRUNCATE TABLE '.$table.';');
                    }
                }
                break;

            default:
                return false;
        }

        if (!$this->debugging) {
            $error = $this->getDatabase()->sql_error();
            if ($error) {
                throw new \Exception(
                    'tx_privacyguard_cleaner failed for table '.$table.' with error: '.$error,
                    1308255491
                );
            }

            if (isset($res)) {
                $this->getDatabase()->sql_free_result($res);
            }
        }

        return $flag;
    }

    /**
     * @param $table
     *
     * @return string
     */
    protected function getWhereClause($table)
    {
        $where = '';
        $timestamp = $this->getWhereTimestamp();

        if ($this->privacyguard_time) {
            switch ($table) {
                case 'tx_pxphpids_log':
                    $where = 'UNIX_TIMESTAMP(created) < '.$timestamp;
                    break;

                case 'sys_log':
                    $where = 'tstamp < '.$timestamp;
                    break;

                default:
                    $where = 'crdate < '.$timestamp;
            }
        }

        return $where;
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
        $fields = array();

        $table = 'tx_formhandler_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extPxPhpids()
    {
        $fields = array();

        $table = 'tx_pxphpids_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * @return bool
     */
    public function extSysLog()
    {
        $fields = array();

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
        $fields = array();

        $table = 'tx_spamshield_log';
        $fields['ip'] = '';

        return $this->processCleaning($table, $fields);
    }

    /**
     * Get database connection.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabase()
    {
        return $GLOBALS['TYPO3_DB'];
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
        return $this->languageService->sL($prefix.$key);
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
     * @param int $status
     */
    protected function log($msg, $status = 1)
    {
        // higher status for debugging
        if ($this->debugging) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($msg);
            $status = 3;
        }
        // write dev log if enabled
        if (TYPO3_DLOG) {
            GeneralUtility::devLog($msg, 'privacyguard', $status);
        }
    }
}
