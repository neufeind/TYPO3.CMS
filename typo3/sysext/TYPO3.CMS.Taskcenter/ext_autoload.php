<?php
// Register necessary class names with autoloader
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Taskcenter');
return array(
	'tx_taskcenter_task' => $extensionPath . 'interfaces/interface.tx_taskcenter_task.php'
);
?>