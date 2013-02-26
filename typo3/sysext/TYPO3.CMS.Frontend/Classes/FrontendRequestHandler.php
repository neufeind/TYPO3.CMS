<?php
namespace TYPO3\CMS\Frontend;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Maroschik <tmaroschik@dfau.de>
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
 * Enter descriptions here
 *
 * @package $PACKAGE$
 * @subpackage $SUBPACKAGE$
 * @scope prototype
 * @entity
 * @api
 */
class FrontendRequestHandler extends \TYPO3\Flow\Http\RequestHandler {

	/**
	 * @var \TYPO3\CMS\Core\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Handles a raw request
	 *
	 * @return void
	 * @api
	 */
	public function handleRequest() {
		// Create the request very early so the Resource Management has a chance to grab it:
		$this->request = \TYPO3\Flow\Http\Request::createFromEnvironment();
		$this->response = new \TYPO3\Flow\Http\Response();
		$this->boot();
		define('TYPO3_MODE', 'FE');
		global $TT, $TSFE, $BE_USER, $TYPO3_CONF_VARS;
		$this->bootstrap
			->startOutputBuffering()
			->loadConfigurationAndInitialize()
			->loadTypo3LoadedExtAndExtLocalconf(TRUE)
			->applyAdditionalConfigurationSettings();

		if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('cms')) {
			die('<strong>Error:</strong> The main frontend extension "cms" was not loaded. Enable it in the extension manager in the backend.');
		}
		// Timetracking started
		if ($_COOKIE[\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::getCookieName()]) {
			require_once PATH_t3lib . 'class.t3lib_timetrack.php';
			$TT = new \TYPO3\CMS\Core\TimeTracker\TimeTracker();
		} else {
			require_once PATH_t3lib . 'class.t3lib_timetracknull.php';
			$TT = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker();
		}
		$TT->start();
		\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->initializeTypo3DbGlobal(FALSE);
		// Hook to preprocess the current request:
		if (is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'])) {
			foreach ($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'] as $hookFunction) {
				$hookParameters = array();
				\TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($hookFunction, $hookParameters, $hookParameters);
			}
			unset($hookFunction);
			unset($hookParameters);
		}
		// Look for extension ID which will launch alternative output engine
		if ($temp_extId = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('eID')) {
			if ($classPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($TYPO3_CONF_VARS['FE']['eID_include'][$temp_extId])) {
				// Remove any output produced until now
				ob_clean();
				require $classPath;
			}
			die;
		}
		// Create $TSFE object (TSFE = TypoScript Front End)
		// Connecting to database
		/**
		 * @var $TSFE \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
		 */
		$TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $TYPO3_CONF_VARS, \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('no_cache'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('cHash'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('jumpurl'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('MP'), \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('RDCT'));
		if ($TYPO3_CONF_VARS['FE']['pageUnavailable_force'] && !\TYPO3\CMS\Core\Utility\GeneralUtility::cmpIP(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'), $TYPO3_CONF_VARS['SYS']['devIPmask'])) {
			$TSFE->pageUnavailableAndExit('This page is temporarily unavailable.');
		}
		$TSFE->connectToDB();
		$TSFE->sendRedirect();
		// Output compression
		// Remove any output produced until now
		ob_clean();
		if ($TYPO3_CONF_VARS['FE']['compressionLevel'] && extension_loaded('zlib')) {
			if (\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($TYPO3_CONF_VARS['FE']['compressionLevel'])) {
				// Prevent errors if ini_set() is unavailable (safe mode)
				@ini_set('zlib.output_compression_level', $TYPO3_CONF_VARS['FE']['compressionLevel']);
			}
			ob_start(array(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Utility\\CompressionUtility'), 'compressionOutputHandler'));
		}
		// FE_USER
		$TT->push('Front End user initialized', '');
		/**
		 * @var $TSFE \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
		 */
		$TSFE->initFEuser();
		$TT->pull();
		// BE_USER
		/**
		 * @var $BE_USER \TYPO3\CMS\Backend\FrontendBackendUserAuthentication
		 */
		$BE_USER = $TSFE->initializeBackendUser();
		// Process the ID, type and other parameters
		// After this point we have an array, $page in TSFE, which is the page-record of the current page, $id
		$TT->push('Process ID', '');
		// Initialize admin panel since simulation settings are required here:
		if ($TSFE->isBackendUserLoggedIn()) {
			$BE_USER->initializeAdminPanel();
		}
		$TSFE->checkAlternativeIdMethods();
		$TSFE->clear_preview();
		$TSFE->determineId();
		// Now, if there is a backend user logged in and he has NO access to this page, then re-evaluate the id shown!
		if ($TSFE->isBackendUserLoggedIn() && (!$BE_USER->extPageReadAccess($TSFE->page) || \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('ADMCMD_noBeUser'))) {
			// \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('ADMCMD_noBeUser') is placed here because workspacePreviewInit() might need to know if a backend user is logged in!
			// Remove user
			unset($BE_USER);
			$TSFE->beUserLogin = 0;
			// Re-evaluate the page-id.
			$TSFE->checkAlternativeIdMethods();
			$TSFE->clear_preview();
			$TSFE->determineId();
		}
		$TSFE->makeCacheHash();
		$TT->pull();
		// Admin Panel & Frontend editing
		if ($TSFE->isBackendUserLoggedIn()) {
			$BE_USER->initializeFrontendEdit();
			if ($BE_USER->adminPanel instanceof \TYPO3\CMS\Frontend\View\AdminPanelView) {
				\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->initializeLanguageObject();
			}
			if ($BE_USER->frontendEdit instanceof \TYPO3\CMS\Core\FrontendEditing\FrontendEditingController) {
				$BE_USER->frontendEdit->initConfigOptions();
			}
		}

		$GLOBALS['TT']->push('Load extension tables');
		\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadExtensionTables(TRUE);
		$GLOBALS['TT']->pull();

		// Starts the template
		$TT->push('Start Template', '');
		$TSFE->initTemplate();
		$TT->pull();
		// Get from cache
		$TT->push('Get Page from cache', '');
		$TSFE->getFromCache();
		$TT->pull();
		// Get config if not already gotten
		// After this, we should have a valid config-array ready
		$TSFE->getConfigArray();
		// Convert POST data to internal "renderCharset" if different from the metaCharset
		$TSFE->convPOSTCharset();
		// Setting language and locale
		$TT->push('Setting language and locale', '');
		$TSFE->settingLanguage();
		$TSFE->settingLocale();
		$TT->pull();
		// Check JumpUrl
		$TSFE->setExternalJumpUrl();
		$TSFE->checkJumpUrlReferer();
		// Check Submission of data.
		// This is done at this point, because we need the config values
		switch ($TSFE->checkDataSubmission()) {
		case 'email':
			$TSFE->sendFormmail();
			break;
		}
		// Check for shortcut page and redirect
		$TSFE->checkPageForShortcutRedirect();
		// Generate page
		$TSFE->setUrlIdToken();
		$TT->push('Page generation', '');
		if ($TSFE->isGeneratePage()) {
			$TSFE->generatePage_preProcessing();
			$temp_theScript = $TSFE->generatePage_whichScript();
			if ($temp_theScript) {
				include $temp_theScript;
			} else {
				include PATH_tslib . 'pagegen.php';
			}
			$TSFE->generatePage_postProcessing();
		} elseif ($TSFE->isINTincScript()) {
			include PATH_tslib . 'pagegen.php';
		}
		$TT->pull();
		// $TSFE->config['INTincScript']
		if ($TSFE->isINTincScript()) {
			$TT->push('Non-cached objects', '');
			$TSFE->INTincScript();
			$TT->pull();
		}
		// Output content
		$sendTSFEContent = FALSE;
		if ($TSFE->isOutputting()) {
			$TT->push('Print Content', '');
			$TSFE->processOutput();
			$sendTSFEContent = TRUE;
			$TT->pull();
		}
		// Store session data for fe_users
		$TSFE->storeSessionData();
		// Statistics
		$TYPO3_MISC['microtime_end'] = microtime(TRUE);
		$TSFE->setParseTime();
		if ($TSFE->isOutputting() && (!empty($TSFE->TYPO3_CONF_VARS['FE']['debug']) || !empty($TSFE->config['config']['debug']))) {
			$TSFE->content .= LF . '<!-- Parsetime: ' . $TSFE->scriptParseTime . 'ms -->';
		}
		// Check JumpUrl
		$TSFE->jumpurl();
		// Preview info
		$TSFE->previewInfo();
		// Hook for end-of-frontend
		$TSFE->hook_eofe();
		// Finish timetracking
		$TT->pull();
		// Check memory usage
		\TYPO3\CMS\Core\Utility\MonitorUtility::peakMemoryUsage();
		// beLoginLinkIPList
		echo $TSFE->beLoginLinkIPList();
		// Admin panel
		if (is_object($BE_USER) && $BE_USER->isAdminPanelVisible() && $TSFE->isBackendUserLoggedIn()) {
			$TSFE->content = str_ireplace('</head>', $BE_USER->adminPanel->getAdminPanelHeaderData() . '</head>', $TSFE->content);
			$TSFE->content = str_ireplace('</body>', $BE_USER->displayAdminPanel() . '</body>', $TSFE->content);
		}
		if ($sendTSFEContent) {
			echo $TSFE->content;
		}
		// Debugging Output
		if (isset($error) && is_object($error) && @is_callable(array($error, 'debugOutput'))) {
			$error->debugOutput();
		}
		if (TYPO3_DLOG) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('END of FRONTEND session', 'cms', 0, array('_FLUSH' => TRUE));
		}
		$this->bootstrap->shutdown('Runtime');
		$this->exit->__invoke();
	}

	/**
	 * Boots up Flow to runtime
	 *
	 * @return void
	 */
	protected function boot() {
//		$sequence = $this->bootstrap->buildEssentialsSequence('runtime');
//		$sequence->invoke($this->bootstrap);
		parent::boot();
	}

	/**
	 * Checks if the request handler can handle the current request.
	 *
	 * @return mixed TRUE or an integer > 0 if it can handle the request, otherwise FALSE or an integer < 0
	 * @api
	 */
	public function canHandleRequest() {
		return TYPO3_MODE === 'FE';
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request. An integer > 0 means "I want to handle this request" where
	 * "100" is default. "0" means "I am a fallback solution".
	 *
	 * @return integer The priority of the request handler
	 * @api
	 */
	public function getPriority() {
		return 125;
	}
}
