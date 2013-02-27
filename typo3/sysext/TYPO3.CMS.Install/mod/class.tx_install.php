<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Contains the class for the Install Tool
 *
 * @author 	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author 	Ingmar Schlecht <ingmar@typo3.org>
 */
// include requirements definition:
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'requirements.php';
// include session handling
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'mod/class.tx_install_session.php';
// include update classes
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/CharsetDefaultsUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/CompatVersionUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/CscSplitUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/NotInMenuUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/MergeAdvancedUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/InstallSysExtsUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/ImagecolsUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/StaticTemplatesUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/T3skinUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/CompressionLevelUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/MigrateWorkspacesUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/FlagsFromSpriteUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/AddFlexFormsToAclUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/ImagelinkUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/MediaFlexformUpdate.php';
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/CoreUpdates/LocalConfigurationUpdate.php';
/*
 * @deprecated since 6.0, the classname tx_install and this file is obsolete
 * and will be removed with 6.2. The class was renamed and is now located at:
 * typo3/sysext/TYPO3.CMS.Install/Classes/Installer.php
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('TYPO3.CMS.Install') . 'Classes/Installer.php';
?>