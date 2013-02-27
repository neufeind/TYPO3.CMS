<?php
/*
 * Register necessary class names with autoloader
 */
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.IndexedSearch');
return array(
	'tx_indexedsearch_indexer' => $extensionPath . 'class.indexer.php',
	'tx_indexedsearch_util' => $extensionPath . 'class.tx_indexedsearch_util.php'
);
?>