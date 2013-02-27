<?php
/*
 * @deprecated since 6.0, the classname tx_saltedpasswords_autoloader and this file is obsolete
 * and will be removed with 6.2. The class was renamed and is now located at:
 * typo3/sysext/TYPO3.CMS.Saltedpasswords/Classes/Autoloader.php
 */
require_once PATH_site . 'typo3/sysext/TYPO3.CMS.Saltedpasswords/Classes/Autoloader.php';
/**
 * @var $SOBE \TYPO3\CMS\Saltedpasswords\Autoloader
 */
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Saltedpasswords\\Autoloader');
$SOBE->execute($this);
?>