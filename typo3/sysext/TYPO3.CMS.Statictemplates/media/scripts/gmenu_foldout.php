<?php
/*
 * @deprecated since 6.0, the classname tslib_gmenu_foldout and this file is obsolete
 * and will be removed with 6.2. The class was renamed and is now located at:
 * typo3/sysext/TYPO3.CMS.Frontend/Classes/ContentObject/Menu/GraphicalMenuFoldout.php
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Frontend') . 'Classes/ContentObject/Menu/GraphicalMenuFoldout.php';
$GLOBALS['TSFE']->tmpl->menuclasses .= ',gmenu_foldout';
?>