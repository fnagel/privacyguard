<?php

/**
 * Class tx_privacyguard_cleaner_addFields
 */
class tx_privacyguard_cleaner_addFields implements tx_scheduler_AdditionalFieldProvider {

	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo Values of the fields from the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
	 * @param tx_scheduler_Module|\TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 *
	 * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {

		// process fields
		if (empty($taskInfo['privacyguard_extkey'])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo['privacyguard_extkey'] = array();
			} elseif ($schedulerModule->CMD == 'edit') {
				$taskInfo['privacyguard_extkey'] = $task->privacyguard_extkey;
			} else {
				$taskInfo['privacyguard_extkey'] = '';
			}
		}
		if (empty($taskInfo['privacyguard_time'])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo['privacyguard_time'] = array();
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
		$fieldID = 'task_privacyguard_extkey';
		$fieldValueArray = $this->getExtensions();

		$fieldCode = '<select name="tx_scheduler[privacyguard_extkey]" id="' . $fieldID . '">';
		foreach ($fieldValueArray as $privacyguard_extkey => $label) {
			$fieldCode .= "\t" . '<option value="' . htmlspecialchars($privacyguard_extkey) . '"' . (($privacyguard_extkey == $taskInfo['privacyguard_extkey']) ? ' selected="selected"' : '') . '>' . $label . '</option>';
		}
		$fieldCode .= '</select>';

		$label = $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_label_extension');
		$label = t3lib_BEfunc::wrapInHelp('privacyguard', $fieldID, $label);
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $label,
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);


		// render time field
		$fieldID = 'task_privacyguard_time';
		$fieldValueArray = $this->getTimes();

		$fieldCode = '<select name="tx_scheduler[privacyguard_time]" id="' . $fieldID . '">';
		foreach ($fieldValueArray as $privacyguard_time => $label) {
			$fieldCode .= "\t" . '<option value="' . htmlspecialchars($privacyguard_time) . '"' . (($privacyguard_time == $taskInfo['privacyguard_time']) ? ' selected="selected"' : '') . '>' . $label . '</option>';
		}
		$fieldCode .= '</select>';

		$label = $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_label_time');
		$label = t3lib_BEfunc::wrapInHelp('privacyguard', $fieldID, $label);
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $label,
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);


		// render method field
		$fieldID = 'task_privacyguard_method';
		$fieldValueArray = $this->getMethods();

		$fieldCode = '<select name="tx_scheduler[privacyguard_method]" id="' . $fieldID . '">';
		foreach ($fieldValueArray as $privacyguard_method => $label) {
			$fieldCode .= "\t" . '<option value="' . htmlspecialchars($privacyguard_method) . '"' . (($privacyguard_method == $taskInfo['privacyguard_method']) ? ' selected="selected"' : '') . '>' . $label . '</option>';
		}
		$fieldCode .= '</select>';

		$label = $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_label_method');
		$label = t3lib_BEfunc::wrapInHelp('privacyguard', $fieldID, $label);
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $label,
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);

		return $additionalFields;
	}

	/**
	 * @todo add a hook here
	 *
	 * @return array
	 */
	public function getExtensions() {
		return array(
			'comments' => 'Commenting system (EXT:comments) ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_notice_alpha'),
			'formhandler' => 'Formhandler (EXT:formhandler)',
			'px_phpids' => 'PHPIDS (EXT:px_phpids)',
			'sfpantispam' => 'Anti Spam (EXT:sfpantispam)',
			've_guestbook' => 'Modern Guestbook (EXT:ve_guestbook) ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_notice_alpha'),
			'sys_log' => 'TYPO3 sys log',
			'spamshield' => 'spamshield (EXT:spamshield)',
		);
	}

	/**
	 * @todo add a hook here
	 *
	 * @return array
	 */
	public function getTimes() {
		return array(
			'0' => $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_all'),
			'24h' => '24 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_h'),
			'48h' => '48 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_h'),
			'72h' => '72 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_h'),
			'7d' => '7 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_d'),
			'14d' => '14 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_d'),
			'1m' => '1 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_m'),
			'3m' => '3 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_m'),
			'6m' => '6 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_m'),
			'12m' => '12 ' . $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_time_m'),
		);
	}

	/**
	 * @todo add a hook here
	 *
	 * @return array
	 */
	public function getMethods() {
		return array(
			'delete_ip' => $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_method_delete_ip'),
			// 'anonymize_ip' => $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_method_anonymize_ip'),
			'delete_all' => $GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_method_delete_all'),
		);
	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param tx_scheduler_Module|\TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 *
	 * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $schedulerModule) {
		$validInput = TRUE;

		// clean data
		$submittedData['privacyguard_extkey'] = trim($submittedData['privacyguard_extkey']);
		$submittedData['privacyguard_time'] = trim($submittedData['privacyguard_time']);


		switch ($submittedData['privacyguard_extkey']) {
			case 'sys_log':
				// do nothing
				break;
			default:
				if (!t3lib_extMgm::isLoaded($submittedData['privacyguard_extkey'])) {
					$schedulerModule->addMessage(
						sprintf($GLOBALS['LANG']->sL('LLL:EXT:privacyguard/lang/locallang.xml:addfields_notice_ext_not_installed'), $submittedData['privacyguard_extkey']),
						t3lib_FlashMessage::ERROR
					);
					$validInput = FALSE;
				}
				break;
		}

		return $validInput;
	}


	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param tx_scheduler_Task|\TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
	 *
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->privacyguard_extkey = $submittedData['privacyguard_extkey'];
		$task->privacyguard_time = $submittedData['privacyguard_time'];
		$task->privacyguard_method = $submittedData['privacyguard_method'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/privacyguard/tasks/class.tx_privacyguard_cleaner_addFields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/privacyguard/tasks/class.tx_privacyguard_cleaner_addFields.php']);
}
?>