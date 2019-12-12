<?php

namespace FelixNagel\PrivacyGuard\Task;

/**
 * This file is part of the "privacyguard" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;

/**
 * Class PrivacyGuardAdditionalFieldProvider.
 */
class PrivacyGuardAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{
    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * Construct class.
     */
    public function __construct()
    {
        $this->languageService = $GLOBALS['LANG'];
    }

    /**
     * Gets additional fields to render in the form to add/edit a task.
     *
     * @param array $taskInfo Values of the fields from the add/edit task form
     * @param \FelixNagel\PrivacyGuard\Task\PrivacyGuardTask $task The task object
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
     *
     * @return array
     */
    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ) {
        // process fields
        if (empty($taskInfo['privacyguard_extkey'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['privacyguard_extkey'] = [];
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['privacyguard_extkey'] = $task->privacyguard_extkey;
            } else {
                $taskInfo['privacyguard_extkey'] = '';
            }
        }
        if (empty($taskInfo['privacyguard_time'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['privacyguard_time'] = [];
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['privacyguard_time'] = $task->privacyguard_time;
            } else {
                $taskInfo['privacyguard_time'] = '';
            }
        }
        if (empty($taskInfo['privacyguard_method'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['privacyguard_method'] = 0;
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['privacyguard_method'] = $task->privacyguard_method;
            } else {
                $taskInfo['privacyguard_method'] = 0;
            }
        }

        // render extkey field
        $fieldId = 'task_privacyguard_extkey';
        $fieldCode = '<select name="tx_scheduler[privacyguard_extkey]" id="'.$fieldId.'">';
        foreach ($this->getExtensions() as $privacyguardExtkey => $label) {
            $fieldCode .= "\t".'<option value="'.htmlspecialchars($privacyguardExtkey).'"'.
                (($privacyguardExtkey == $taskInfo['privacyguard_extkey']) ? ' selected="selected"' : '').
                '>'.$label.'</option>';
        }
        $fieldCode .= '</select>';

        $additionalFields[$fieldId] = [
            'code' => $fieldCode,
            'label' => BackendUtility::wrapInHelp('privacyguard', $fieldId, $this->translate('addfields_label_extension')),
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];

        // render time field
        $fieldId = 'task_privacyguard_time';
        $fieldCode = '<select name="tx_scheduler[privacyguard_time]" id="'.$fieldId.'">';
        foreach ($this->getTimes() as $privacyguardTime => $label) {
            $fieldCode .= "\t".'<option value="'.htmlspecialchars($privacyguardTime).'"'.
                (($privacyguardTime == $taskInfo['privacyguard_time']) ? ' selected="selected"' : '').
                '>'.$label.'</option>';
        }
        $fieldCode .= '</select>';

        $additionalFields[$fieldId] = [
            'code' => $fieldCode,
            'label' => BackendUtility::wrapInHelp('privacyguard', $fieldId, $this->translate('addfields_label_time')),
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];

        // render method field
        $fieldId = 'task_privacyguard_method';
        $fieldCode = '<select name="tx_scheduler[privacyguard_method]" id="'.$fieldId.'">';
        foreach ($this->getMethods() as $privacyguardMethod => $label) {
            $fieldCode .= "\t".'<option value="'.
                htmlspecialchars($privacyguardMethod).'"'.
                (($privacyguardMethod == $taskInfo['privacyguard_method']) ? ' selected="selected"' : '').
                '>'.$label.'</option>';
        }
        $fieldCode .= '</select>';

        $additionalFields[$fieldId] = [
            'code' => $fieldCode,
            'label' => BackendUtility::wrapInHelp('privacyguard', $fieldId, $this->translate('addfields_label_method')),
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $fieldId,
        ];

        return $additionalFields;
    }

    /**
     * @todo add a hook here
     *
     * @return array
     */
    public function getExtensions()
    {
        return PrivacyGuardTask::$supportedExtensions;
    }

    /**
     * @todo add a hook here
     *
     * @return array
     */
    public function getTimes()
    {
        return [
            '0' => $this->translate('addfields_time_all'),
            '24h' => '24 '.$this->translate('addfields_time_h'),
            '48h' => '48 '.$this->translate('addfields_time_h'),
            '72h' => '72 '.$this->translate('addfields_time_h'),
            '7d' => '7 '.$this->translate('addfields_time_d'),
            '14d' => '14 '.$this->translate('addfields_time_d'),
            '1m' => '1 '.$this->translate('addfields_time_m'),
            '3m' => '3 '.$this->translate('addfields_time_m'),
            '6m' => '6 '.$this->translate('addfields_time_m'),
            '12m' => '12 '.$this->translate('addfields_time_m'),
        ];
    }

    /**
     * @todo add a hook here
     *
     * @return array
     */
    public function getMethods()
    {
        return [
            'delete_ip' => $this->translate('addfields_method_delete_ip'),
            // 'anonymize_ip' => $this->translate('addfields_method_anonymize_ip'),
            'delete_all' => $this->translate('addfields_method_delete_all'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function validateAdditionalFields(
        array &$submittedData,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ) {
        $validInput = true;

        // clean data
        $submittedData['privacyguard_extkey'] = trim($submittedData['privacyguard_extkey']);
        $submittedData['privacyguard_time'] = trim($submittedData['privacyguard_time']);

        switch ($submittedData['privacyguard_extkey']) {
            case 'sys_log':
                // do nothing
                break;

            default:
                if (!ExtensionManagementUtility::isLoaded($submittedData['privacyguard_extkey'])) {
                    $this->addMessage(
                        sprintf($this->translate('addfields_notice_ext_not_installed'), $submittedData['privacyguard_extkey']),
                        FlashMessage::ERROR
                    );
                    $validInput = false;
                }
        }

        return $validInput;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task)
    {
        $task->privacyguard_extkey = $submittedData['privacyguard_extkey'];
        $task->privacyguard_time = $submittedData['privacyguard_time'];
        $task->privacyguard_method = $submittedData['privacyguard_method'];
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
}
