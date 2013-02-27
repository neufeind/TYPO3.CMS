<?php
// Register necessary class names with autoloader
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Sv');
return array(
	'tx_sv_reports_serviceslist' => $extensionPath . 'reports/class.tx_sv_reports_serviceslist.php'
);
?>